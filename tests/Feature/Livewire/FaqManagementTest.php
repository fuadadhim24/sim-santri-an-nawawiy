<?php

namespace Tests\Feature\Livewire;

use App\Livewire\FaqIndex;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FaqManagementTest extends TestCase
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
            ->test(FaqIndex::class)
            ->assertStatus(200);
    }

    public function test_can_delete_faq()
    {
        $faq = Faq::create([
            'title' => 'Apa itu SIM Santri?',
            'content' => 'Sistem Informasi Manajemen Santri.',
            'category' => 'umum',
            'is_active' => true,
            'order' => 1
        ]);

        Livewire::actingAs($this->admin)
            ->test(FaqIndex::class)
            ->call('delete', $faq->id);

        $this->assertDatabaseMissing('faqs', [
            'id' => $faq->id
        ]);
    }

    public function test_can_toggle_active_status()
    {
        $faq = Faq::create([
            'title' => 'Test Toggle?',
            'content' => 'Toggle Answer.',
            'category' => 'umum',
            'is_active' => true,
            'order' => 1
        ]);

        Livewire::actingAs($this->admin)
            ->test(FaqIndex::class)
            ->call('toggleActive', $faq->id);

        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'is_active' => false
        ]);
    }

    public function test_can_create_pengumuman_category()
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\FaqForm::class)
            ->set('title', 'Libur Lebaran')
            ->set('content', 'Libur mulai tanggal 1 Syawal.')
            ->set('category', 'pengumuman')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('faqs', [
            'title' => 'Libur Lebaran',
            'category' => 'pengumuman',
            'is_active' => true
        ]);
    }

    public function test_faq_sort_order_is_saved()
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\FaqForm::class)
            ->set('title', 'FAQ Sort Order Test')
            ->set('content', 'Content 2')
            ->set('category', 'umum')
            ->set('sort_order', 99)
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $faq2 = Faq::where('title', 'FAQ Sort Order Test')->first();
        $this->assertNotNull($faq2);
        $this->assertEquals(99, $faq2->sort_order);
    }
}
