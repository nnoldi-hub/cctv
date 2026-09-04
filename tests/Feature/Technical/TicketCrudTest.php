<?php

namespace Tests\Feature\Technical;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $techUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->techUser = User::factory()->create();
        $this->techUser->assignRole('tehnic');
    }

    public function test_technician_can_create_a_ticket(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->techUser)->post(route('technical.tickets.store'), [
            'client_id' => $client->id,
            'subject' => 'Camera exterior nu porneste',
            'description' => 'Clientul semnaleaza ca o camera nu mai porneste dupa o pana de curent.',
            'priority' => 'high',
            'status' => 'open',
        ]);

        $ticket = Ticket::firstWhere('client_id', $client->id);
        $response->assertRedirect(route('technical.tickets.show', $ticket));
        $this->assertDatabaseHas('tickets', ['subject' => 'Camera exterior nu porneste']);
    }

    public function test_technician_can_add_a_comment_to_a_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this->actingAs($this->techUser)
            ->post(route('technical.tickets.comments', $ticket), ['body' => 'Am contactat clientul, urmeaza vizita.'])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->techUser->id,
            'body' => 'Am contactat clientul, urmeaza vizita.',
        ]);
    }

    public function test_technician_can_change_ticket_status(): void
    {
        $ticket = Ticket::factory()->create(['status' => 'open']);

        $this->actingAs($this->techUser)
            ->patch(route('technical.tickets.status', $ticket), ['status' => 'resolved'])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'resolved']);
    }

    public function test_tickets_can_be_filtered_by_status(): void
    {
        Ticket::factory()->create(['status' => 'open']);
        Ticket::factory()->create(['status' => 'closed']);

        $response = $this->actingAs($this->techUser)
            ->get(route('technical.tickets.index', ['status' => 'closed']));

        $response->assertInertia(fn ($page) => $page
            ->component('Technical/Tickets/Index')
            ->has('tickets.data', 1)
        );
    }
}
