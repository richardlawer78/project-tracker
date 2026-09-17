<?php

namespace Database\Seeders;

use App\Models\{BacklogItem, BudgetItem, ChangeLogEntry, ChatChannel, ChatMessage, Document, DorDodItem, Kickoff, KickoffObjective, LessonLearned, Milestone, Project, ProjectResource, Risk, Sprint, Stakeholder, Task, TestCase, TimeEntry, User, Workflow};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory()->count(7)->create();
        $users[0]->update(['name' => 'Test User', 'email' => 'test@example.com', 'role' => 'admin']);
        $projects = Project::factory()->count(5)->recycle($users)->create();

        foreach ($projects as $project) {
            $members = $users->random(3);
            $tasks = Task::factory()->count(4)->state(fn () => ['project_id' => $project->id, 'assigned_to' => $members->random()->id])->create();
            Sprint::factory()->count(2)->state(['project_id' => $project->id])->create();
            BacklogItem::factory()->count(3)->state(['project_id' => $project->id])->create();
            Workflow::factory()->create(['project_id' => $project->id]);
            Milestone::factory()->count(2)->state(['project_id' => $project->id])->create();
            BudgetItem::factory()->count(2)->state(['project_id' => $project->id])->create();
            foreach ($members as $member) { ProjectResource::factory()->create(['project_id' => $project->id, 'user_id' => $member->id]); }
            TimeEntry::factory()->count(3)->state(fn () => ['project_id' => $project->id, 'task_id' => $tasks->random()->id, 'user_id' => $members->random()->id])->create();
            Stakeholder::factory()->count(2)->state(['project_id' => $project->id])->create();
            $kickoff = Kickoff::factory()->create(['project_id' => $project->id]);
            KickoffObjective::factory()->count(3)->state(['kickoff_id' => $kickoff->id])->create();
            DorDodItem::factory()->count(2)->state(['project_id' => $project->id, 'list_type' => 'dor'])->create();
            DorDodItem::factory()->count(2)->state(['project_id' => $project->id, 'list_type' => 'dod'])->create();
            TestCase::factory()->count(2)->state(['project_id' => $project->id])->create();
            Risk::factory()->count(2)->state(fn () => ['project_id' => $project->id, 'owner_id' => $members->random()->id])->create();
            ChangeLogEntry::factory()->count(2)->state(fn () => ['project_id' => $project->id, 'requestor_id' => $members->random()->id])->create();
            Document::factory()->count(2)->state(fn () => ['project_id' => $project->id, 'uploaded_by' => $members->random()->id])->create();
            LessonLearned::factory()->count(2)->state(['project_id' => $project->id])->create();
            $channel = ChatChannel::factory()->create(['project_id' => $project->id]);
            ChatMessage::factory()->count(3)->state(fn () => ['chat_channel_id' => $channel->id, 'user_id' => $members->random()->id])->create();
        }
    }
}
