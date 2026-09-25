<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ProjectMemberController extends Controller
{
    private const ROLES = ['member', 'developer', 'team-lead', 'project-manager'];

    /**
     * Add (invite) a person to a project.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorizeTeamManagement($request, $project);

        $isOther = $request->input('user_id') === 'other';

        $data = $request->validate([
            'user_id' => ['required', $isOther ? 'in:other' : 'exists:users,id'],
            'email' => [$isOther ? 'required' : 'nullable', 'email'],
            'role' => ['required', 'in:'.implode(',', self::ROLES)],
        ]);

        if ($isOther) {
            $email = strtolower(trim($data['email']));
            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            // No account yet -> email them a join link.
            if (! $user) {
                return $this->sendJoinLink($request, $project, $email, $data['role']);
            }
        } else {
            $user = User::findOrFail($data['user_id']);
        }

        $alreadyMember = $project->members()
            ->where('users.id', $user->id)
            ->exists();

        if ($alreadyMember) {
            return back()->with('success', 'That person is already on this project.');
        }

        $project->members()->attach($user->id, ['role' => $data['role']]);

        $message = $user->name.' was added to the project';

        // Email the person so they can open the project.
        try {
            $user->notify(new ProjectInvitation(
                $project,
                $request->user()->name,
                $data['role']
            ));

            $message .= ' and an invitation email was sent to '.$user->email.'.';
        } catch (\Throwable $e) {
            report($e);

            $message .= ', but the invitation email could not be sent. Check the mail settings.';
        }

        return back()->with('success', $message);
    }

    /**
     * Opened from an invite link (signed URL). Adds the logged-in user.
     */
    public function join(Request $request, Project $project)
    {
        $role = in_array($request->query('role'), self::ROLES, true)
            ? $request->query('role')
            : 'member';

        $user = $request->user();

        $alreadyMember = $project->members()
            ->where('users.id', $user->id)
            ->exists();

        if (! $alreadyMember) {
            $project->members()->attach($user->id, ['role' => $role]);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', $alreadyMember
                ? 'You are already on this project.'
                : 'Welcome! You have joined "'.$project->name.'".');
    }

    /**
     * Remove a person from a project.
     */
    public function destroy(Request $request, Project $project, User $user)
    {
        $this->authorizeTeamManagement($request, $project);

        $project->members()->detach($user->id);

        return back()->with('success', $user->name.' was removed from the project.');
    }

    /**
     * Email a join link to someone who has no account yet.
     */
    private function sendJoinLink(Request $request, Project $project, string $email, string $role)
    {
        $link = URL::signedRoute('projects.join', [
            'project' => $project,
            'role' => $role,
        ]);

        $roleLabel = ucwords(str_replace('-', ' ', $role));
        $inviter = $request->user()->name;

        $body = "{$inviter} has invited you to join the project \"{$project->name}\" as {$roleLabel}.\n\n"
            ."Open this link, then log in or create an account to join:\n{$link}\n";

        try {
            Mail::raw($body, function ($mail) use ($email, $project) {
                $mail->to($email)->subject('You have been invited to "'.$project->name.'"');
            });

            return back()->with('success', 'Invitation link emailed to '.$email.'.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('success', 'Could not send the email. Check the mail settings, or share the invite link instead.');
        }
    }

    /**
     * Only admins, project managers and the project owner can manage the team.
     */
    private function authorizeTeamManagement(Request $request, Project $project): void
    {
        $user = $request->user();

        abort_unless(
            $user && (
                $user->role === 'admin'
                || $user->role === 'project-manager'
                || $project->owner_id === $user->id
            ),
            403,
            'You are not allowed to manage this project\'s team.'
        );
    }
}