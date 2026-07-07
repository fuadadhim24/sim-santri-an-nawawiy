<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ClassLevel;
use App\Models\StudyGroup;
use App\Models\Student;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use Illuminate\Support\Facades\DB;

class RombelManagement extends Component
{
    public $showClassLevelModal = false;
    public $showStudyGroupModal = false;
    public $showManageStudentsModal = false;

    public $classLevelId;
    public $classLevelName;
    public $classLevelOrder = 0;

    public $studyGroupId;
    public $targetClassLevelId;
    public $studyGroupName;
    public $studyGroupCapacity = 40;

    // Move students billing properties
    public $moveBillingPolicy = 'none'; // 'none', 'delete_all_and_new', 'delete_except_month_and_new'
    public $moveBillingCategories = [];
    public $availableMoveBillings = [];

    protected $rules = [
        'classLevelName' => 'required|string|max:255',
        'classLevelOrder' => 'required|integer',
        'studyGroupName' => 'required|string|max:255',
        'studyGroupCapacity' => 'required|integer|min:1',
    ];

    public function openClassLevelModal($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $level = ClassLevel::find($id);
            $this->classLevelId = $level->id;
            $this->classLevelName = $level->name;
            $this->classLevelOrder = $level->level_order;
        } else {
            $this->classLevelId = null;
            $this->classLevelName = '';
            $this->classLevelOrder = ClassLevel::max('level_order') + 1;
        }
        $this->showClassLevelModal = true;
    }

    public function saveClassLevel()
    {
        $this->validate([
            'classLevelName' => 'required|string|max:255',
            'classLevelOrder' => 'required|integer',
        ]);

        ClassLevel::updateOrCreate(
            ['id' => $this->classLevelId],
            [
                'name' => $this->classLevelName,
                'level_order' => $this->classLevelOrder,
            ]
        );

        $this->showClassLevelModal = false;
        $this->dispatch('swal:success', ['title' => 'Tersimpan!', 'text' => 'Tingkat Kelas berhasil disimpan.']);
    }

    public function openStudyGroupModal($classLevelId, $id = null)
    {
        $this->resetValidation();
        $this->targetClassLevelId = $classLevelId;
        
        if ($id) {
            $group = StudyGroup::find($id);
            $this->studyGroupId = $group->id;
            $this->studyGroupName = $group->name;
            $this->studyGroupCapacity = $group->max_capacity;
        } else {
            $this->studyGroupId = null;
            $this->studyGroupName = '';
            $this->studyGroupCapacity = 40;
        }
        $this->showStudyGroupModal = true;
    }

    public function saveStudyGroup()
    {
        $this->validate([
            'studyGroupName' => 'required|string|max:255',
            'studyGroupCapacity' => 'required|integer|min:1',
            'targetClassLevelId' => 'required|exists:class_levels,id',
        ]);

        StudyGroup::updateOrCreate(
            ['id' => $this->studyGroupId],
            [
                'class_level_id' => $this->targetClassLevelId,
                'name' => $this->studyGroupName,
                'max_capacity' => $this->studyGroupCapacity,
            ]
        );

        $this->showStudyGroupModal = false;
        $this->dispatch('swal:success', ['title' => 'Tersimpan!', 'text' => 'Rombel berhasil disimpan.']);
    }

    public $manageSourceType; 
    public $manageSourceId;
    public $manageSourceName;
    public $selectedStudentIds = [];
    public $targetMoveId = '';

    public $modalStudents = [];

    public function openManageStudentsModal($type, $id, $name)
    {
        $this->manageSourceType = $type;
        $this->manageSourceId = $id;
        $this->manageSourceName = $name;
        $this->selectedStudentIds = [];
        $this->targetMoveId = '';
        $this->moveBillingPolicy = 'none';
        $this->moveBillingCategories = [];
        $this->availableMoveBillings = [];

        if ($type === 'unassigned_global') {
            $this->modalStudents = Student::where('is_active', true)
                ->where('status', 'diterima')
                ->whereNull('class_level_id')
                ->get();
        } elseif ($type === 'unassigned_level') {
            $this->modalStudents = Student::where('is_active', true)
                ->where('status', 'diterima')
                ->where('class_level_id', $id)
                ->whereNull('study_group_id')
                ->get();
        } elseif ($type === 'rombel') {
            $this->modalStudents = Student::where('is_active', true)
                ->where('status', 'diterima')
                ->where('study_group_id', $id)
                ->get();
        }

        $this->showManageStudentsModal = true;
    }

    public function isSameClassLevel()
    {
        if (empty($this->targetMoveId)) {
            return true;
        }

        $sourceClassLevelId = null;
        if ($this->manageSourceType === 'unassigned_level') {
            $sourceClassLevelId = $this->manageSourceId;
        } elseif ($this->manageSourceType === 'rombel') {
            $rombel = StudyGroup::find($this->manageSourceId);
            $sourceClassLevelId = $rombel?->class_level_id;
        }

        $targetClassLevelId = null;
        if (str_starts_with($this->targetMoveId, 'unassigned_level_')) {
            $targetClassLevelId = str_replace('unassigned_level_', '', $this->targetMoveId);
        } elseif (str_starts_with($this->targetMoveId, 'rombel_')) {
            $rombelId = str_replace('rombel_', '', $this->targetMoveId);
            $rombel = StudyGroup::find($rombelId);
            $targetClassLevelId = $rombel?->class_level_id;
        }

        return $sourceClassLevelId == $targetClassLevelId;
    }

    public function updatedTargetMoveId($value)
    {
        $this->availableMoveBillings = [];
        $this->moveBillingCategories = [];
        
        $classLevelId = null;
        if (str_starts_with($value, 'unassigned_level_')) {
            $classLevelId = str_replace('unassigned_level_', '', $value);
        } elseif (str_starts_with($value, 'rombel_')) {
            $rombelId = str_replace('rombel_', '', $value);
            $rombel = StudyGroup::find($rombelId);
            $classLevelId = $rombel?->class_level_id;
        }

        $sourceClassLevelId = null;
        if ($this->manageSourceType === 'unassigned_level') {
            $sourceClassLevelId = $this->manageSourceId;
        } elseif ($this->manageSourceType === 'rombel') {
            $rombel = StudyGroup::find($this->manageSourceId);
            $sourceClassLevelId = $rombel?->class_level_id;
        }

        if ($classLevelId && $sourceClassLevelId != $classLevelId) {
            $this->availableMoveBillings = FeeCategory::where('is_active', true)
                ->where('is_locked', false)
                ->with(['fees' => function ($q) use ($classLevelId) {
                    $q->where('is_active', true)
                      ->where('class_level_target_id', $classLevelId);
                }])
                ->get()
                ->filter(fn($cat) => $cat->fees->count() > 0)
                ->map(function ($category) {
                    return [
                        'id' => (string) $category->id,
                        'name' => $category->name,
                        'total_amount' => $category->fees->sum('amount')
                    ];
                })
                ->toArray();
            
            $this->moveBillingCategories = array_map(fn($b) => $b['id'], $this->availableMoveBillings);
        }
    }

    public function selectAllStudents()
    {
        if (count($this->selectedStudentIds) === count($this->modalStudents)) {
            $this->selectedStudentIds = [];
        } else {
            $this->selectedStudentIds = $this->modalStudents->pluck('id')->map(fn($id) => (string)$id)->toArray();
        }
    }

    public function moveSelectedStudents()
    {
        if (empty($this->selectedStudentIds)) {
            $this->dispatch('swal:info', ['title' => 'Peringatan', 'text' => 'Pilih minimal 1 santri untuk dipindahkan.']);
            return;
        }

        if (empty($this->targetMoveId)) {
            $this->dispatch('swal:info', ['title' => 'Peringatan', 'text' => 'Pilih tujuan pemindahan.']);
            return;
        }

        $updates = [];
        $classLevelId = null;
        
        if (str_starts_with($this->targetMoveId, 'unassigned_level_')) {
            $levelId = str_replace('unassigned_level_', '', $this->targetMoveId);
            $updates = [
                'class_level_id' => $levelId,
                'study_group_id' => null,
            ];
            $classLevelId = $levelId;
        } elseif (str_starts_with($this->targetMoveId, 'rombel_')) {
            $rombelId = str_replace('rombel_', '', $this->targetMoveId);
            $rombel = StudyGroup::find($rombelId);
            if ($rombel) {
                $updates = [
                    'class_level_id' => $rombel->class_level_id,
                    'study_group_id' => $rombel->id,
                ];
                $classLevelId = $rombel->class_level_id;
            }
        }

        $sourceClassLevelId = null;
        if ($this->manageSourceType === 'unassigned_level') {
            $sourceClassLevelId = $this->manageSourceId;
        } elseif ($this->manageSourceType === 'rombel') {
            $rombel = StudyGroup::find($this->manageSourceId);
            $sourceClassLevelId = $rombel?->class_level_id;
        }

        if (!empty($updates)) {
            $students = Student::whereIn('id', $this->selectedStudentIds)->get();
            $billingService = app(\App\Services\BillingService::class);

            DB::transaction(function () use ($students, $updates, $billingService, $sourceClassLevelId, $classLevelId) {
                foreach ($students as $student) {
                    $student->update($updates);

                    // Only run transition if target class level is different from source class level
                    if ($sourceClassLevelId != $classLevelId && $this->moveBillingPolicy !== 'none') {
                        $policy = $this->moveBillingPolicy === 'delete_all_and_new' ? 'delete_all' : ($this->moveBillingPolicy === 'delete_except_month_and_new' ? 'delete_except_current_month' : 'keep_all');
                        
                        $billingService->transitionStudentBillings(
                            $student,
                            $policy,
                            [],
                            $this->moveBillingCategories
                        );
                    }
                }
            });

            $count = count($this->selectedStudentIds);
            $this->showManageStudentsModal = false;
            $this->dispatch('swal:success', [
                'title' => 'Berhasil!', 
                'text' => "$count santri berhasil dipindahkan."
            ]);
        }
    }

    public $showPromotionWizard = false;
    public $wizardStep = 1;
    
    public $sourceLevelId = '';
    public $destinationLevelId = ''; 
    
    public $promotionMethod = 'kosong';
    public $paralelMapping = []; 
    public $sourceStudyGroups = [];
    
    public $promotionDraft = []; 
    public $destinationStudyGroups = []; 

    // Wizard billing transition properties
    public $wizardBillingCategories = [];
    public $availableWizardBillings = [];
    public $wizardBillingPolicy = 'none'; // 'none', 'delete_all', 'delete_except_current_month', 'graduation_keep_unpaid', 'graduation_delete_unpaid'

    public function openPromotionWizard()
    {
        $this->resetValidation();
        $this->showPromotionWizard = true;
        $this->wizardStep = 1;
        $this->sourceLevelId = '';
        $this->destinationLevelId = '';
        $this->promotionMethod = 'kosong';
        $this->promotionDraft = [];
        $this->paralelMapping = [];
        $this->wizardBillingCategories = [];
        $this->availableWizardBillings = [];
        $this->wizardBillingPolicy = 'none';
    }

    public function wizardNextStep()
    {
        if ($this->wizardStep === 1) {
            $this->validate([
                'sourceLevelId' => 'required',
                'destinationLevelId' => 'required|different:sourceLevelId',
            ], [
                'destinationLevelId.different' => 'Kelas asal dan tujuan tidak boleh sama.'
            ]);
            
            $this->sourceStudyGroups = StudyGroup::where('class_level_id', $this->sourceLevelId)->get()->toArray();
            if ($this->destinationLevelId !== 'lulus') {
                $this->destinationStudyGroups = StudyGroup::where('class_level_id', $this->destinationLevelId)->get()->toArray();
            } else {
                $this->destinationStudyGroups = [];
            }
            
            $this->paralelMapping = [];
            if ($this->destinationLevelId !== 'lulus') {
                foreach ($this->sourceStudyGroups as $sourceRombel) {
                    $sourceName = $sourceRombel['name'];
                    $match = collect($this->destinationStudyGroups)->firstWhere('name', $sourceName);
                    
                    if (!$match) {
                        $sourceSuffix = preg_replace('/^([0-9IVX]+\s*[-_]?\s*)/i', '', $sourceName);
                        
                        $match = collect($this->destinationStudyGroups)->first(function($destRombel) use ($sourceSuffix) {
                            $destSuffix = preg_replace('/^([0-9IVX]+\s*[-_]?\s*)/i', '', $destRombel['name']);
                            return strtolower($destSuffix) === strtolower($sourceSuffix);
                        });
                    }
                    
                    $this->paralelMapping[$sourceRombel['id']] = $match ? $match['id'] : '';
                }
            }
            
            if ($this->destinationLevelId === 'lulus') {
                $this->generatePromotionDraft();
                $this->loadWizardBillings();
                $this->wizardStep = 3;
            } else {
                $this->wizardStep = 2;
            }
        } elseif ($this->wizardStep === 2) {
            $this->generatePromotionDraft();
            $this->loadWizardBillings();
            $this->wizardStep = 3;
        }
    }

    public function loadWizardBillings()
    {
        $this->availableWizardBillings = [];
        $this->wizardBillingCategories = [];

        if ($this->destinationLevelId === 'lulus') {
            $this->wizardBillingPolicy = 'graduation_keep_unpaid';
        } else {
            $this->wizardBillingPolicy = 'none';
            $classLevelId = $this->destinationLevelId;

            $this->availableWizardBillings = FeeCategory::where('is_active', true)
                ->where('is_locked', false)
                ->with(['fees' => function ($q) use ($classLevelId) {
                    $q->where('is_active', true)
                      ->where('class_level_target_id', $classLevelId);
                }])
                ->get()
                ->filter(fn($cat) => $cat->fees->count() > 0)
                ->map(function ($category) {
                    return [
                        'id' => (string) $category->id,
                        'name' => $category->name,
                        'total_amount' => $category->fees->sum('amount')
                    ];
                })
                ->toArray();
            
            $this->wizardBillingCategories = array_map(fn($b) => $b['id'], $this->availableWizardBillings);
        }
    }

    public function wizardPrevStep()
    {
        if ($this->wizardStep > 1) {
            if ($this->wizardStep === 3 && $this->destinationLevelId === 'lulus') {
                $this->wizardStep = 1;
            } else {
                $this->wizardStep--;
            }
        }
    }

    public function generatePromotionDraft()
    {
        $students = Student::with('studyGroup')
            ->where('is_active', true)
            ->where('status', 'diterima')
            ->where('class_level_id', $this->sourceLevelId)
            ->get();

        $draft = [];
        $isLulus = ($this->destinationLevelId === 'lulus');
        
        $destRombels = [];
        if (!$isLulus) {
            $destRombels = StudyGroup::withCount(['students' => function($q) {
                $q->where('is_active', true)->where('status', 'diterima');
            }])->where('class_level_id', $this->destinationLevelId)->get();
            $this->destinationStudyGroups = $destRombels->toArray();
        }

        if ($this->promotionMethod === 'acak' && !$isLulus) {
            $students = $students->shuffle();
            
            $rombelBuckets = [];
            foreach ($destRombels as $dr) {
                $available = max(0, $dr->max_capacity - $dr->students_count);
                $rombelBuckets[$dr->id] = $available;
            }
            
            foreach ($students as $student) {
                arsort($rombelBuckets);
                $targetRombelId = key($rombelBuckets);
                
                if ($rombelBuckets[$targetRombelId] > 0) {
                    $rombelBuckets[$targetRombelId]--;
                } else {
                    $targetRombelId = null; 
                }

                $draft[$student->id] = [
                    'name' => $student->full_name,
                    'old_rombel' => $student->studyGroup ? $student->studyGroup->name : '-',
                    'new_level_id' => $this->destinationLevelId,
                    'new_rombel_id' => $targetRombelId,
                    'skip' => false
                ];
            }
        } else {
            foreach ($students as $student) {
                $targetRombelId = null;

                if ($this->promotionMethod === 'paralel' && !$isLulus && $student->study_group_id) {
                    $targetRombelId = $this->paralelMapping[$student->study_group_id] ?? null;
                    if ($targetRombelId === '') {
                        $targetRombelId = null;
                    }
                }

                $draft[$student->id] = [
                    'name' => $student->full_name,
                    'old_rombel' => $student->studyGroup ? $student->studyGroup->name : '-',
                    'new_level_id' => $isLulus ? null : $this->destinationLevelId,
                    'new_rombel_id' => $targetRombelId,
                    'skip' => false
                ];
            }
        }

        $this->promotionDraft = $draft;
    }

    public function executePromotion()
    {
        $count = 0;
        $isLulus = ($this->destinationLevelId === 'lulus');
        $billingService = app(\App\Services\BillingService::class);

        DB::transaction(function () use ($isLulus, $billingService, &$count) {
            foreach ($this->promotionDraft as $studentId => $data) {
                if ($data['skip']) {
                    continue;
                }

                $student = Student::find($studentId);
                if (!$student) continue;

                if ($isLulus) {
                    $student->update([
                        'status' => 'lulus',
                        'is_active' => false,
                        'class_level_id' => null,
                        'study_group_id' => null,
                    ]);

                    // If graduation policy is delete all unpaid
                    if ($this->wizardBillingPolicy === 'graduation_delete_unpaid') {
                        $unpaid = $billingService->getUnpaidBillings($student);
                        foreach ($unpaid as $bill) {
                            $bill->delete();
                        }
                    }
                } else {
                    $student->update([
                        'class_level_id' => $data['new_level_id'],
                        'study_group_id' => $data['new_rombel_id'],
                    ]);

                    // Execute billing transitions for promoted student
                    if ($this->wizardBillingPolicy !== 'none') {
                        $billingService->transitionStudentBillings(
                            $student,
                            $this->wizardBillingPolicy,
                            [],
                            $this->wizardBillingCategories
                        );
                    }
                }
                $count++;
            }
        });

        $this->showPromotionWizard = false;
        $this->dispatch('swal:success', [
            'title' => 'Eksekusi Selesai!', 
            'text' => "$count santri berhasil di" . ($isLulus ? 'luluskan.' : 'naikkan kelas.')
        ]);
    }

    public function render()
    {
        $classLevels = ClassLevel::with(['studyGroups' => function($q) {
            $q->withCount(['students' => function($sq) {
                $sq->where('is_active', true)->where('status', 'diterima');
            }]);
        }])->orderBy('level_order')->get();

        $unassignedToRombel = [];
        foreach ($classLevels as $level) {
            $unassignedToRombel[$level->id] = Student::where('is_active', true)
                ->where('status', 'diterima')
                ->where('class_level_id', $level->id)
                ->whereNull('study_group_id')
                ->get();
        }

        return view('livewire.rombel-management', [
            'classLevels' => $classLevels,
            'unassignedToRombel' => $unassignedToRombel,
        ])->layout('layouts.admin');
    }
}
