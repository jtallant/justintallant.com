date: "2024-06-18"
title: How Should We Structure Our Laravel Apps?
---
Project structure is a hot topic that has likely challenged us all throughout our careers. It's common to find ourselves questioning where a particular piece of code should go, which can be disruptive to the development process. This constant self-questioning often signals that our project is either already disorganized or on its way there. We've encountered numerous architectural approaches at conferences and on YouTube over the years.

Unfortunately, many of these are over-engineered and suggest decoupling from the framework we're using. Over time, most of us have come to agree that decoupling from essential elements like the framework itself or the framework ORM is counterproductive. While many solutions are overly complex, just sticking with the default structure opens the door to a lot of problems down the road. In this post I will outline my goals for a good project structure and what I'm using right now.
<br />
<br />
## What I want in project structure
- The structure itself describes the application
- Simple and repeatable
- Stops me from asking the question "Where does this go?"
- Flows well with Laravel
    - Does not decouple me from the framework
    - No unnecessary headaches
- Encourages best practices by default
    - New developers to the project intuitively understand where things go
    - Discourages large classes
- Does not require big design decisions up front
    - Things are easily moved around as the app grows
<br />
<br />
## What I've landed on for now

#### Top level directories describe high level concepts
The top level folders of the application tell a clear story about the purpose of the application and what components it has.

#### Sub folders contain framework or development specific concepts
Framework specific folder names below top level concepts make it easier for developers to make decisions about where code goes. Code is also easier to find.

Let's pretend we are recreating the project management software Basecamp. We'll only use two larger components in our example - projects and organizations. With just these two components alone, we actually have a decent number of files and if we stubbed out the rest of the components, we'd have a lot more. I think this is a good argument for not just dumping everything under app/Models, app/Http, and app/Jobs.

<br />
#### Basecamp (Project Management) Example
<pre><code class="language-markdown">
* Organizations
    - Organization.php
    - Actions
        - CreateOrganization.php
        - RemoveMember.php
    - Commands
        - PruneExpiredOrgs.php
    - Jobs
        - SendOrganizationInvite.php
        - ArchiveOrganization.php
        - RestoreOrganization.php
    - Http
        - Controller
            - OrganizationsController.php
            - OrganizationInvitesController.php
        - Middleware
            - RedirectIfNoAccess.php
    - Providers
        - OrganizationsProvider.php
    - Queries
        - GetOrganizationsWithActiveProjectsAndMemberCounts.php
    - resources/
    - routes.php
* Projects
    - Project.php
    - Actions
        - CreateProject.php
        - ArchiveProject.php
        - RestoreProject.php
        - InviteUser.php
        - RemoveUser.php
        - MoveProject.php
    - Commands
        - PruneExpiredProjects.php
    - Jobs
        - SendProjectInvite.php
    - Http
        - Controller
            - ProjectsController.php
            - ProjectInvitesController.php
        - Middleware
            - RedirectIfNoAccess.php
    - Mail
        - ProjectInviteEmail.php
    - Providers
        - ProjectsProvider.php
    - resources/
    - routes.php
</code></pre>
<br />

#### Top Level Clarity
Immediately upon opening the app, you will see top level folders **Projects** and **Organizations** which will signal to you that we're probably doing some project management here. In a real world example, there would be several other top level directories that would tell a more complete story. Perhaps **Todos**, **Users**, **MessageBoards**, and more. Now let's take a look at the subfolders (framework specific folders)

#### Framework Specific Folders
* Actions
    - perform state changes that aren't queued
    - a good place to put code you would put in a service class
    - does one thing
* Jobs
    - similar to actions but queued
    - not console commands
* Commands
    - similar to actions and jobs
    - always available in console as a command
    - can be queued but don't have to be
    - might simply dispatch a queued job
* Query
    - We're not using CQRS
    - This is just where we put more complex queries
    - Otherwise we just use Eloquent directly
* Provider
    - Registers console commands, add things to scheduler, etc.
    - Ties the module to the framework in ways you would a package.
* resources
    - Views and other resources for this module
* routes.php
    - The routes for this module

<br />
#### By structuring our projects in this way, we achieve several key benefits
- Enhanced clarity and organization
- Improved maintainability
- Easier onboarding for new developers
- Reduced cognitive load during development

<br />
#### Downsides
- Requires knowledge of service providers
    - You have to configure the framework to load things like resources
- I don't think this adds much complexity at all
    - It's the same or easier than configuring a package

<br />
#### Why no service classes?
While service classes can sometimes help in organizing code, they often lead to an anti-pattern where a single class becomes a dumping ground for too much logic. Generically named classes tend to attract generic code. This is why I prefer the structure outlined above, which avoids generic "FooService" classes and encourages more specific, maintainable components.

<br />
#### What about Notifications, Events/Listeners, FormRequests, etc?
Those would all go inside the module they relate to. So the would be sibling directories of Commands, Query, etc.

<br />
#### Repositories?
The structure outlined above doesn't mention anything about repositories. You could use repositories with this or not. The modern consensus with Laravel is to not use repositories and I agree with that. I would only use repositories if I knew up front I would be using something alongside Eloquent to such a degree that using it separately in actions, jobs, etc wouldn't be enough.

<br />
#### What do we call this structure?
I'm calling it **Framework Aligned Architecture**. While we aren't using the default structure of the framework, we are in alignment with the framework structure and make references to its built in features in many of our folder names (jobs, commands, etc). These folder names match directly with concepts in the Laravel documentation. This approach just takes the default structure and applies it to specific modules that tell a story and provide guidance. No need to get geometry involved and start drawing hexagons 😉.

<br />
This structure is working well for me right now, but I'm always open to new ideas. What project structures are you using, or what changes would you suggest? Let me know in the comments!