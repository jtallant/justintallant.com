title: Laravel Anti-Patterns and How to Protect Your Codebase From Itself
date: "2026-06-09"
category: Software Architecture
---

Laravel is a genuinely great framework. It has a polished DX, an enormous ecosystem, and it gets you productive fast. It's also full of traps.

Most of those traps share a common shape. Laravel makes the wrong thing convenient. The framework hands you a feature, it feels ergonomic and clever, and six months later you're untangling a spaghetti codebase wondering how you got here. This post is about recognizing those traps before you fall in.

## 1. Anemic Models (On Purpose)

The word "anemic" is usually an insult in software circles. Here, it's the goal.

A model in my codebase contains exactly two things. Casts and a docblock that documents every column on the table.

<pre><code class="language-php">
/**
 * @property int $id
 * @property string $identifier
 * @property string $title
 * @property string $status       backlog|in_progress|done|cancelled
 * @property string $priority     urgent|high|medium|low
 * @property Carbon|null $due_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Issue extends Model
{
    protected $casts = [
        'due_date'   => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
</code></pre>

That's it. No scopes, no accessors, no mutators, no relationships, no business logic.

**Why?** Because Laravel gives you a dozen ways to stuff behavior into a model, and less experienced developers will use all of them. Before long your `Issue` model has a `scopeActive()`, a `getPriorityLabelAttribute()`, a `notifyAssignee()`, a `syncCycleProgress()`, and a relationship chain that touches eight other tables. It becomes a god object with no clear owner and no clear boundary.

Hard-restricting models to casts and docblocks forces every other decision to be made deliberately. Where does this query go? Where does this business logic live? Those are the right questions to be asking.

**Keeping it in sync.** The docblock goes stale fast if you don't enforce it. I use CodeRabbit paths to flag the model for docblock review any time a new migration touches that table. The AI catches it on every PR before it ships.

## 2. Don't Use the ORM for Queries. Marry Your Database

The Eloquent pitch has always rested on two ideas. First, that you might need to swap databases someday, so an abstraction layer protects you. Second, that a fluent object interface is easier to read and write than raw SQL.

After fifteen-plus years of building applications, I think both of these are wrong in practice.

**On portability.** Nobody swaps databases. You pick Postgres or MySQL at the start of a project and you run it for the life of the app. The abstraction buys you nothing in exchange for hiding what your code actually does.

**On readability.** Raw SQL is more readable than Eloquent for anything beyond trivial lookups. AI assistants are also significantly better at writing and debugging SQL than they are at Eloquent. That matters when you're generating most of your code through prompts.

The bigger issue is what Eloquent hides from you. Every fluent chain you write gets compiled into SQL, and that SQL has real performance implications. Eloquent is notorious for making N+1 queries invisible. A developer loading a board of seventy issues with their assignees, labels, and cycle info can write three innocent-looking lines of Eloquent and ship a page that fires over 200 queries. One base query plus three relationship lookups per row. With raw SQL, that same mistake is hard to miss.

<pre><code class="language-php">
// It is immediately obvious something is wrong here
$commentsByIssue = [];
foreach ($issues as $issue) {
    $commentsByIssue[$issue->id] = DB::select('SELECT * FROM comments WHERE issue_id = ?', [$issue->id]);
}
</code></pre>

With Eloquent, the same N+1 hides behind a `->comments` property access inside a blade template or a resource class. It looks fine. It isn't.

**Where I still use Eloquent.** Model lookups and writes. `Issue::find($id)`, `$issue->status = 'in_progress'`, `$issue->save()`, `$issue->update($attributes)`. These are fine. The ORM is a thin wrapper around a row, and that's a reasonable use of it. Everything else is SQL.

Yes, SQL is verbose. Verbose is easier to debug than magic. And keystrokes haven't mattered since LLMs. I'm not typing this stuff anyway. I'm describing what I want and reviewing what comes back. I'd rather review SQL than review Eloquent that's hiding a query plan problem from me.

## 3. Kill the Repository Pattern

The repository pattern shows up in Laravel codebases in two flavors, and both are worth avoiding.

**Flavor one. The query dumping ground.** A `UserRepository` or `IssueRepository` that accumulates every query related to that model. This sounds organized until you realize that in any real application, a concept like "user" or "issue" touches everything. In a project management tool like Linear:

- Users are assigned issues
- Users belong to teams
- Users participate in cycles
- Users receive mention notifications
- Users have workspace roles and permissions
- Users appear in activity feeds

A `UserRepository` in that context has no natural boundary. Developers don't know where to stop, so they don't. They keep adding methods until the repository is a 600-line file that's half queries and half state changes and fully unmaintainable. The FK relationship between `issues.assignee_id` and `users.id` is not a good reason to put issue-assignment logic in `UserRepository`.

**Flavor two. The Eloquent decoupling pattern.** Abstracting behind an interface so you can swap Eloquent out. This was a conversation in 2015. If you're marrying your database (see section 2), there's nothing to decouple from. It's dead weight.

## 4. Organize by Concept, Not by Table

The default Laravel directory structure is fine for a side project you're going to abandon in three months. For anything long-lived, piling every controller into `app/Http/Controllers` and every service into `app/Services` organizes your codebase around technical artifacts instead of application concepts.

The problem is that tables have no conceptual boundaries. Consider something like Linear, a project management tool with issues, cycles, teams, and roadmaps:

- `User` shows up in authentication, team membership, issue assignment, notifications, mentions, billing, and audit logs
- `Issue` shows up in cycle planning, the roadmap view, project milestones, search, and activity feeds
- `Team` shows up in access control, cycle management, settings, reporting, and label configuration

Instead, structure by application concept using a module layout under `src/Modules`. Notice that the modules below are not named after tables. There's no `Issues/` module. There's `IssueTracking` and `IssueAssignment`, because those are two distinct concepts that both happen to involve the `issues` table. The table is an implementation detail. The module is a slice of behavior. Keeping that distinction forces clarity about what a module actually owns and leaves the door open to split further as the application grows.

Models are the exception. They stay in `src/Models`, because a model doesn't belong to any single module. The same `Issue` model is read by `IssueTracking` and written by `IssueAssignment`. It's a shared, anemic row wrapper (see section 1), not behavior that lives inside one slice. Everything that does belong to a slice goes in the module. Controllers, actions, queries.

<pre><code class="language-markdown">
src/
  Models/
    Issue.php
    Cycle.php
    Team.php
    Member.php
  Modules/
    IssueAssignment/
      Http/
        Controllers/
          AssignIssueController.php
          UnassignIssueController.php
          ReassignIssueController.php
      Actions/
        AssignIssue.php
        UnassignIssue.php
        ReassignIssue.php
      Queries/
        GetAssigneeWorkload.php
        GetIssuesAssignedToUser.php
    IssueTracking/
      Http/
        Controllers/
          CreateIssueController.php
          UpdateIssueStatusController.php
          ArchiveIssueController.php
          ShowIssueController.php
          ShowBoardController.php
          ShowBacklogController.php
      Actions/
        CreateIssue.php
        UpdateIssueStatus.php
        ArchiveIssue.php
      Queries/
        GetBoardIssues.php
        GetBacklogIssues.php
        GetIssueDetail.php
    CyclePlanning/
      Http/
        Controllers/
          StartCycleController.php
          CloseCycleController.php
          MoveIssueToCycleController.php
          RemoveIssueFromCycleController.php
      Actions/
        StartCycle.php
        CloseCycle.php
        MoveIssueToCycle.php
        RemoveIssueFromCycle.php
      Queries/
        GetCycleProgress.php
        GetCycleIssues.php
    TeamManagement/
      Http/
        Controllers/
          CreateTeamController.php
          AddTeamMemberController.php
          RemoveTeamMemberController.php
          UpdateMemberRoleController.php
      Actions/
        CreateTeam.php
        AddTeamMember.php
        RemoveTeamMember.php
        UpdateMemberRole.php
      Queries/
        GetTeamWorkload.php
        GetTeamMembers.php
</code></pre>

Each module owns a slice of the application's behavior. The files inside tell you what the application *does*, not what tables it has. You can onboard a new developer by pointing them at a module and having them understand it without reading half the codebase.

## 5. Query Classes Instead of Repositories

Once you abandon repositories, a question remains. Where do queries live?

Query classes. One class, one query, one public method.

<pre><code class="language-php">
class GetBoardIssues
{
    public function __construct(private readonly PDO $pdo) {}

    /** @return list&lt;array&lt;string, mixed&gt;&gt; */
    public function execute(int $teamId, string $cycleId): array
    {
        $sql = '
            SELECT
                i.id,
                i.identifier,
                i.title,
                i.status,
                i.priority,
                i.sort_order,
                i.due_date,
                u.id   AS assignee_id,
                u.name AS assignee_name,
                u.avatar_url AS assignee_avatar,
                (SELECT COUNT(*) FROM comments c WHERE c.issue_id = i.id) AS comment_count
            FROM issues i
            LEFT JOIN users u ON u.id = i.assignee_id
            WHERE i.team_id  = :team_id
              AND i.cycle_id  = :cycle_id
              AND i.deleted_at IS NULL
            ORDER BY i.sort_order ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['team_id' => $teamId, 'cycle_id' => $cycleId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
</code></pre>

This is it. The class name describes exactly what it does. There's no ambiguity about scope. It can't grow into a dumping ground because there's nothing to dump into. It does one thing.

**On duplication.** If you need a similar query for a different context, say `GetBacklogIssues` which skips the cycle filter and sorts differently, don't reach for a shared `IssueQueryBuilder` with a `$cycleId = null` parameter and branching logic inside. Copy the query, change what differs. Two files, two queries, zero coupling.

This feels wrong to developers who've been trained to see duplication as the enemy. But duplication is cheap. Coupling is expensive. Refactoring duplication into a shared abstraction is easy. You do it when the pattern is obvious and stable. Refactoring coupling is hard, especially when the shared function has been called from eight different places and each caller needs it to behave slightly differently. That's how you get a function with six boolean parameters and a switch statement inside it.

Every public function is a liability. Every time you make something shared, you've created a dependency that someone will eventually need to change without breaking the other callers. Duplication keeps things independent.

Don't be afraid of files. A hundred focused, single-purpose query classes is a better codebase than twenty bloated repositories.

## 6. Action Classes for State Changes. Kill Your Services

Query classes handle reads. What handles writes?

Not service classes. Services are the new repositories. They start focused and end up as dumping grounds. `IssueService` sounds reasonable until it has forty methods covering creation, assignment, status transitions, cycle management, archiving, and bulk operations. Nobody knows what goes in it and what doesn't, so everything does. The boundary is "things that involve issues," which is no boundary at all.

The same principle that makes query classes work applies here. One class, one public method, one reason to exist. Action classes.

<pre><code class="language-php">
class CreateIssue
{
    public function __construct(private readonly PDO $pdo) {}

    public function execute(int $teamId, int $creatorId, string $title, string $priority): int
    {
        $identifier = $this->nextIdentifier($teamId);

        $stmt = $this->pdo->prepare('
            INSERT INTO issues (team_id, creator_id, identifier, title, priority, status, created_at, updated_at)
            VALUES (:team_id, :creator_id, :identifier, :title, :priority, :status, NOW(), NOW())
        ');

        $stmt->execute([
            'team_id'    => $teamId,
            'creator_id' => $creatorId,
            'identifier' => $identifier,
            'title'      => $title,
            'priority'   => $priority,
            'status'     => 'backlog',
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    private function nextIdentifier(int $teamId): string
    {
        // ...
    }
}
</code></pre>

<pre><code class="language-php">
class UpdateIssueStatus
{
    public function __construct(private readonly PDO $pdo) {}

    public function execute(int $issueId, string $status): void
    {
        $completedAt = $status === 'done' ? 'NOW()' : 'NULL';

        $this->pdo->prepare("
            UPDATE issues
            SET status = :status, completed_at = {$completedAt}, updated_at = NOW()
            WHERE id = :id
        ")->execute(['status' => $status, 'id' => $issueId]);
    }
}
</code></pre>

<pre><code class="language-php">
class AssignIssue
{
    public function __construct(private readonly PDO $pdo) {}

    public function execute(int $issueId, int $assigneeId): void
    {
        $this->pdo->prepare('
            UPDATE issues SET assignee_id = :assignee_id, updated_at = NOW() WHERE id = :id
        ')->execute(['assignee_id' => $assigneeId, 'id' => $issueId]);
    }
}
</code></pre>

Three classes, three behaviors, each independently readable and testable. If `AssignIssue` needs to also dispatch a notification, that change is local. If `UpdateIssueStatus` needs special handling when transitioning to `cancelled`, you add it here without touching anything else.

Compare this to finding that logic buried in method 23 of an `IssueService` that's been accumulating behavior for two years.

**What about shared logic?** Same answer as query classes. Duplication over coupling. If `CreateIssue` and `CloneIssue` both need to compute the next identifier, that's a legitimate private helper or a small dedicated class. Not a reason to merge the two actions or build a shared service. Keep the shared thing small and specific. The moment "shared logic" means "this class does two things," you've lost.

The controller becomes a thin router. Resolve the action from the container, call `execute()`, return the response. That's it.

<pre><code class="language-php">
class CreateIssueController extends Controller
{
    public function __invoke(CreateIssueRequest $request, CreateIssue $action): JsonResponse
    {
        $issueId = $action->execute(
            teamId:    $request->integer('team_id'),
            creatorId: $request->user()->id,
            title:     $request->string('title'),
            priority:  $request->string('priority'),
        );

        return response()->json(['id' => $issueId], 201);
    }
}
</code></pre>

The controller has no idea how an issue gets created. It just passes validated input to the thing that knows, and returns what comes back. That's the right level of abstraction for a controller.

## 7. Ditch JSON Resources. Use DTOs

Laravel's JSON resources exist to transform models into API responses. They have a reputation for elegant code. They also make it trivially easy to ship N+1 queries you won't notice until your API slows down in production.

The pattern is insidious because it looks clean.

<pre><code class="language-php">
class IssueResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'identifier' => $this->identifier,
            'title'      => $this->title,
            'assignee'   => $this->assignee->name,         // 🚨 query per issue
            'labels'     => $this->labels->pluck('name'),  // 🚨 query per issue
            'comment_count' => $this->comments->count(),   // 🚨 query per issue
        ];
    }
}
</code></pre>

Load a board view with fifty issues and you've just fired 151 queries without writing a single loop. The framework hid it from you.

The alternative is simple. Write the queries explicitly, pull what the card needs in a fixed, predictable number of them, and map each row into a DTO with a `toArray()` method.

<pre><code class="language-php">
readonly class IssueCardDto
{
    public function __construct(
        public int         $id,
        public string      $identifier,
        public string      $title,
        public string      $status,
        public string      $priority,
        public int|null    $assigneeId,
        public string|null $assigneeName,
        public string|null $assigneeAvatar,
        public int         $commentCount,
        /** @var list&lt;string&gt; */
        public array       $labels,
    ) {}

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'identifier' => $this->identifier,
            'title'      => $this->title,
            'status'     => $this->status,
            'priority'   => $this->priority,
            'assignee'   => $this->assigneeId ? [
                'id'     => $this->assigneeId,
                'name'   => $this->assigneeName,
                'avatar' => $this->assigneeAvatar,
            ] : null,
            'comment_count' => $this->commentCount,
            'labels'        => $this->labels,
        ];
    }
}
</code></pre>

Your controller calls `GetBoardIssues`, maps each row into an `IssueCardDto`, and returns `array_map(fn($dto) => $dto->toArray(), $dtos)`. The scalar fields, including the comment count, come back in that one query. Labels are a many-to-many relationship, so they can't collapse into a single row. They get their own query, batched across every issue on the board with `WHERE issue_id IN (...)` and grouped by issue in PHP. That's two queries total, and the count stays flat whether the board has five issues or five hundred. The villain above fired 151 for fifty.

You can also read the exact API contract straight from the DTO constructor without running the app.

### A note on `return $model`

While JSON resources are bad, `return $model` is worse. And it's everywhere in the Laravel ecosystem. Imagine a `/issues/{id}` endpoint that just returns the `Issue` model. You're now serializing `team_id`, `cycle_id`, `assignee_id`, `sort_order`, `deleted_at`, and a dozen other columns the client never asked for. That includes any you add to the table six months from now. You have no idea at a glance what your endpoint actually returns. You can't see the contract. Someone has to open the inspector, load the page, and spelunk through the response to figure out what fields exist. It's the laziest possible API design, and Laravel makes it the path of least resistance by auto-serializing models to JSON.

Convenience isn't always a feature. Sometimes it's just technical debt with good marketing.

## The Through-Line

All of these anti-patterns share the same root. Laravel optimizes for the first thirty minutes of a feature, not the next three years. Eloquent relationships are elegant until you're debugging query counts. JSON resources are clean until you're profiling a slow endpoint. Service classes feel organized until they're 800 lines and nobody can tell you what belongs in them. The default directory structure feels tidy until your app grows past a certain size.

The discipline required is just this. When the framework hands you something convenient, ask what it's hiding from you and what it'll cost you later. Usually the answer is "more than it's worth."

More patterns to come.
