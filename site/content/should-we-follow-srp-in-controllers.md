title: Should we Follow SRP in Controllers?
date: "2024-06-03"
---
You probably already know it - The Single Responsibility Principle, one of the five SOLID principles of object-oriented design. It states that a class should have only one reason to change, meaning it should only have one job or responsibility. This principle is touted as essential for writing clean, maintainable, and scalable code.

But do we need to follow SRP in MVC controllers? Controllers are pretty gluey by nature so it's an interesting question if you ask me. I've heard Taylor himself say that he often does validation right inside the controller which means he doesn't always follow SRP in controllers. For a controller to be truly SRP adherent, it would be solely responsible for returning a response, not making queries or handling validation. Let's take a look at a non SRP controller, and the setup for converting it into an SRP controller and decide for ourselves.

#### Our non SRP example &mdash; a common Laravel controller

You've probably seen or wrote this two dozen times. Our example is a user registration controller where we validate input, create a user, and send a welcome email.

<pre><code class="language-php">
class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Send a welcome email
        Mail::to($user->email)->send(new WelcomeMail($user));

        return response()->json([]);
    }
}
</code></pre>

To adhere to the Single Responsibility Principle, we have to refactor our code by separating these responsibilities into different classes.

1. **Request Validation**: Create a Form Request class.
2. **User Creation**: Create a service class to handle user creation.
3. **Welcome Email**: Use en event listener.

#### Refactored SRP Example

<pre><code class="language-php">
class RegisterUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}

class UserRepository
{
    public function create(array $data)
    {
        return User::create($data);
    }
}

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendWelcomeEmail::class,
        ],
    ];
}

class SendWelcomeEmail
{
    public function handle(UserRegistered $event)
    {
        Mail::to($event->user->email)
            ->send(new WelcomeMail($event->user));
    }
}

class UserController extends Controller
{
    protected $users;
    protected $events;

    public function __construct(UserRepository $users, Dispatcher $events)
    {
        $this->users = $users;
        $this->events = $events;
    }

    public function register(RegisterUserRequest $request)
    {
        $this->users->create($request->validated());

        $this->events->dispatch(new UserRegistered($user));

        return response()->json([]);
    }
}
</code></pre>

#### Conclusion

Whew! Ok... That's a lot more code. We went from one class to 5 classes but we are now following SRP. For me personally, I think it's easier to reason about the application if we just violate SRP and stick with the first example. I prefer not having to switch between files if I don't have to. That doesn't mean I want giant procedural files and don't like to use classes. Let's not take it to the extreme.

On any small application, I would likely forgo form requests and events as much as I can and I wouldn't be using repositories either. However, on larger applications I will definitely be using the SRP conformant example. Yes I will have to move around between files but with larger apps that's going to be easier in the long run than having cluttered controllers.

I'd like to point out that for smaller apps, I will extract to classes when the controller method starts to look a little too big. And a little too big for me really isn't that big. I can't give you an exact line number, it's more of a feel.

Another argument in favor of reasonable violation of SRP in controllers is that Laravel's testing tools are robust enough to account for these situations and testability isn't a problem here for either example.

<div class="comments">
    <h4>Comments</h4>
    <p class="disclaimer">
        Comments are fake and for entertainment purposes only.
    </p>
    <div class="comment">
        <div class="comment-author">
            <div class="author-img"></div>
            <span class="author-name">AssHat1</span>
        </div>
        <div class="comment-content">
            <p>
                Wow, this is one of the dumbest posts I've ever read. You clearly have no idea what you're talking about. Ignoring SRP in controllers is a recipe for disaster, and anyone with half a brain knows that. You're just promoting bad practices and laziness. Do everyone a favor and stop giving advice you clearly don't understand.
            </p>
        </div>

        <div class="comment">
            <div class="comment-author">
                <img class="author-img" src="/img/justin-icon.jpg" />
                <span class="author-name">Author</span>
            </div>
            <div class="comment-content">
                <p>
                    Thank you for that insightful comment AssHat! I love your username.
                </p>
            </div>
        </div> <!-- .comment -->
    </div> <!-- .comment -->
    <div class="comment">
        <div class="comment-author">
            <div class="author-img"></div>
            <span class="author-name">NiceGuy7</span>
        </div>
        <div class="comment-content">
            <p>
                Hey there! Great post! 😊 I totally agree with your take on SRP in controllers. Sometimes, sticking strictly to SRP can add unnecessary complexity, especially for smaller projects. Your method definitely reduces cognitive overhead by keeping everything in one place. I appreciate how you highlighted the balance between maintainability and simplicity. Your examples were clear and made it easy to see the benefits of both approaches. Keep up the awesome work! Looking forward to more of your insightful posts. 👍
            </p>
        </div>
    </div> <!-- .comment -->
    <div class="comment">
        <div class="comment-author">
            <div class="author-img"></div>
            <span class="author-name">I'mJustHere17</span>
        </div>
        <div class="comment-content">
            <p>
                Nice post.
            </p>
        </div>
    </div> <!-- .comment -->
</div>
