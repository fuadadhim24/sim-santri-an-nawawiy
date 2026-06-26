<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ClassLevel;
use App\Models\StudyGroup;
use App\Models\Student;

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
        
        if (str_starts_with($this->targetMoveId, 'unassigned_level_')) {
            $levelId = str_replace('unassigned_level_', '', $this->targetMoveId);
            $updates = [
                'class_level_id' => $levelId,
                'study_group_id' => null,
            ];
        } elseif (str_starts_with($this->targetMoveId, 'rombel_')) {
            $rombelId = str_replace('rombel_', '', $this->targetMoveId);
            $rombel = StudyGroup::find($rombelId);
            if ($rombel) {
                $updates = [
                    'class_level_id' => $rombel->class_level_id,
                    'study_group_id' => $rombel->id,
                ];
            }
        }

        if (!empty($updates)) {
            Student::whereIn('id', $this->selectedStudentIds)->update($updates);
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
                $this->wizardStep = 3;
            } else {
                $this->wizardStep = 2;
            }
        } elseif ($this->wizardStep === 2) {
            $this->generatePromotionDraft();
            $this->wizardStep = 3;
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

        foreach ($this->promotionDraft as $studentId => $data) {
            if ($data['skip']) {
                continue;
            }

            if ($isLulus) {
                Student::where('id', $studentId)->update([
                    'status' => 'lulus',
                    'is_active' => false,
                    'class_level_id' => null,
                    'study_group_id' => null,
                ]);
            } else {
                Student::where('id', $studentId)->update([
                    'class_level_id' => $data['new_level_id'],
                    'study_group_id' => $data['new_rombel_id'],
                ]);
            }
            $count++;
        }

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

        $unassignedStudents = Student::where('is_active', true)
            ->where('status', 'diterima')
            ->whereNull('class_level_id')
            ->get();

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
            'unassignedStudents' => $unassignedStudents,
            'unassignedToRombel' => $unassignedToRombel,
        ])->layout('layouts.admin');
    }
}
