<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RombelManagement;
use App\Models\ClassLevel;
use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RombelManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
    }

    public function test_renders_successfully()
    {
        Livewire::actingAs($this->admin)
            ->test(RombelManagement::class)
            ->assertStatus(200);
    }

    public function test_can_create_class_level()
    {
        Livewire::actingAs($this->admin)
            ->test(RombelManagement::class)
            ->set('classLevelName', 'Kelas 1')
            ->set('classLevelOrder', 1)
            ->call('saveClassLevel');

        $this->assertDatabaseHas('class_levels', [
            'name' => 'Kelas 1'
        ]);
    }

    public function test_can_create_study_group()
    {
        $level = ClassLevel::create(['name' => 'Kelas 1', 'order' => 1]);

        Livewire::actingAs($this->admin)
            ->test(RombelManagement::class)
            ->call('openStudyGroupModal', $level->id)
            ->set('studyGroupName', '1-A')
            ->set('studyGroupCapacity', 30)
            ->call('saveStudyGroup');

        $this->assertDatabaseHas('study_groups', [
            'name' => '1-A',
            'max_capacity' => 30,
            'class_level_id' => $level->id
        ]);
    }

    public function test_can_edit_study_group()
    {
        $level = ClassLevel::create(['name' => 'Kelas 1', 'order' => 1]);
        $rombel = StudyGroup::create(['name' => '1-A', 'max_capacity' => 30, 'class_level_id' => $level->id]);

        Livewire::actingAs($this->admin)
            ->test(RombelManagement::class)
            ->call('openStudyGroupModal', $level->id, $rombel->id)
            ->assertSet('studyGroupName', '1-A')
            ->assertSet('studyGroupCapacity', 30)
            ->set('studyGroupName', '1-A Edited')
            ->set('studyGroupCapacity', 25)
            ->call('saveStudyGroup');

        $this->assertDatabaseHas('study_groups', [
            'id' => $rombel->id,
            'name' => '1-A Edited',
            'max_capacity' => 25,
            'class_level_id' => $level->id
        ]);
    }

    public function test_can_move_students_to_rombel()
    {
        $level = ClassLevel::create(['name' => 'Kelas 1', 'order' => 1]);
        $rombel = StudyGroup::create(['name' => '1-A', 'max_capacity' => 30, 'class_level_id' => $level->id]);
        
        $student = Student::factory()->create(['is_active' => true, 'status' => 'diterima', 'class_level_id' => null, 'study_group_id' => null]);

        Livewire::actingAs($this->admin)
            ->test(RombelManagement::class)
            ->set('selectedStudentIds', [$student->id])
            ->set('targetMoveId', 'rombel_' . $rombel->id)
            ->call('moveSelectedStudents');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'class_level_id' => $level->id,
            'study_group_id' => $rombel->id
        ]);
    }

    public function test_promotion_wizard_lulus_method()
    {
        $level = ClassLevel::create(['name' => 'Kelas 3', 'order' => 3]);
        $rombel = StudyGroup::create(['name' => '3-A', 'max_capacity' => 30, 'class_level_id' => $level->id]);
        
        $student = Student::factory()->create([
            'is_active' => true, 
            'status' => 'diterima', 
            'class_level_id' => $level->id, 
            'study_group_id' => $rombel->id
        ]);

        Livewire::actingAs($this->admin)
            ->test(RombelManagement::class)
            ->set('sourceLevelId', $level->id)
            ->set('destinationLevelId', 'lulus')
            ->set('promotionMethod', 'kosong')
            ->call('wizardNextStep') // Go to step 2
            ->call('wizardNextStep') // Go to step 3 (Draft generation)
            ->call('executePromotion'); // Execute graduation

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'lulus',
            'is_active' => false,
            'class_level_id' => null,
            'study_group_id' => null
        ]);
    }

    public function test_promotion_wizard_parallel_method()
    {
        $sourceLevel = ClassLevel::create(['name' => 'Kelas 1', 'order' => 1]);
        $destLevel = ClassLevel::create(['name' => 'Kelas 2', 'order' => 2]);
        
        $sourceGroup = StudyGroup::create(['name' => '1-A', 'max_capacity' => 30, 'class_level_id' => $sourceLevel->id]);
        $destGroup = StudyGroup::create(['name' => '2-A', 'max_capacity' => 30, 'class_level_id' => $destLevel->id]);
        
        $student = Student::factory()->create([
            'is_active' => true, 
            'status' => 'diterima',
            'class_level_id' => $sourceLevel->id, 
            'study_group_id' => $sourceGroup->id
        ]);

        Livewire::actingAs($this->admin)
            ->test(RombelManagement::class)
            ->set('sourceLevelId', $sourceLevel->id)
            ->set('destinationLevelId', $destLevel->id)
            ->set('promotionMethod', 'paralel')
            ->call('wizardNextStep')
            ->set('paralelMapping.' . $sourceGroup->id, $destGroup->id) // Map 1-A to 2-A
            ->call('wizardNextStep')
            ->call('executePromotion');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'class_level_id' => $destLevel->id,
            'study_group_id' => $destGroup->id
        ]);
    }

    public function test_promotion_wizard_acak_method_capacity_handling()
    {
        $sourceLevel = ClassLevel::create(['name' => 'Kelas 1', 'order' => 1]);
        $destLevel = ClassLevel::create(['name' => 'Kelas 2', 'order' => 2]);
        
        $sourceGroup = StudyGroup::create(['name' => '1-A', 'max_capacity' => 30, 'class_level_id' => $sourceLevel->id]);
        
        // Dest groups with small capacity to test overflow/balancing
        $destGroup1 = StudyGroup::create(['name' => '2-A', 'max_capacity' => 1, 'class_level_id' => $destLevel->id]);
        $destGroup2 = StudyGroup::create(['name' => '2-B', 'max_capacity' => 2, 'class_level_id' => $destLevel->id]);
        
        // Create 3 students
        $students = Student::factory()->count(3)->create([
            'is_active' => true, 
            'status' => 'diterima',
            'class_level_id' => $sourceLevel->id, 
            'study_group_id' => $sourceGroup->id
        ]);

        Livewire::actingAs($this->admin)
            ->test(RombelManagement::class)
            ->set('sourceLevelId', $sourceLevel->id)
            ->set('destinationLevelId', $destLevel->id)
            ->set('promotionMethod', 'acak')
            ->call('wizardNextStep')
            ->call('wizardNextStep')
            ->call('executePromotion');

        // Since capacities are 1 and 2, and we have 3 students, they should fit exactly.
        $this->assertEquals(1, Student::where('study_group_id', $destGroup1->id)->count());
        $this->assertEquals(2, Student::where('study_group_id', $destGroup2->id)->count());
    }
}
