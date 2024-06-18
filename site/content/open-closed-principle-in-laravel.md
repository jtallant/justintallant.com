date: "2024-06-17"
title: The Open/Closed Principle in Laravel
---
The Open/Closed principle is the second principle of the SOLID acronym and is the broadest principle of SOLID. Because it so broad it can seem a little vague when you are first learning about it but it's actually a very simple concept.

The official statement is as follows:

"Software entities (classes, modules, functions, etc.) should be open for extension but closed for modification."

When you think of open/closed, you can basically just think "I want to be able to add new code instead of changing existing code."

Because, the statement is so broad, you can demonstrate adherence to it in many different patterns. Out the box, Laravel encourages adherence to open/closed through it's basic features. Let's take a look at the service container and service providers and see how they make use of open/closed.

#### The Service Container
The service container is probably the most important tool in Laravel or any MVC framework. The container is essential for extending Laravel app functionality via packages but it should also be utilized by your own application. One could write several pages on the container alone but let's just look at a basic example of how the container allows you to add code without modifying code.

Let's say your company decided to roll their own support ticketing system that allows customers to submit issues when using your main product. You want to use AI (an LLM) to summarize a ticket succinctly when it comes in from one of your valued users so your team can act faster on it with better comprehension.

Right now the team wants to use Open AI's API to generate the ticket summary but they are considering switching to Llama3 in the future because they can self host a Llama3 instance for free. The team doesn't have time to configure Llama3 right now and the quickest way to get it done is through Open AI so you need to write that implementation now.

You know you're probably going to have to switch to Llama soon so you don't want to have to change a bunch of code next month. In order to accomplish this, you create a **TicketSummarizerInterface** and bind an **OpenAITicketSummarizer** to it in the container. This allows you to type hint **TicketSummarizerInterface** in your application and receive an **OpenAITicketSummarizer** instance. Now when you switch to Llama3 next month, you only have to change one line of code and you can simply add a **LlamaTicketSummarizer** class that implements the **TicketSummarizerInterface**.

#### Create a TicketSummarizerInterface
<pre><code class="language-php">
// app/LLM/TicketSummarizer/TicketSummarizerInterface.php
interface TicketSummarizerInterface
{
    public function summarize(string $content): string
}
</code></pre>

#### Create an OpenAITicketSummarizer
<pre><code class="language-php">
// app/LLM/TicketSummarizer/OpenAITicketSummarizer.php
class OpenAITicketSummarizer implements TicketSummarizerInterface
{
    private $openAIApi;

    public function __construct($openAIApi)
    {
        $this->openAIApi = $openAiApi;
    }

    public function summarize(string $content): string
    {
        // send api request to open ai using a prompt and the ticket content
        // return the string response
    }
}
</code></pre>

#### Create a Service Provider
<pre><code class="language-php">
// app/LLM/TicketSummarizer/TickerSummarizerProvider.php
class TicketSummarizerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(TicketSummarizerInterface::class, function ($app) {

            // Let's say OpenAiApi is a package we installed
            $openAIApi = $app->make('OpenAIApi');

            return new OpenAITicketSummarizer($openAIApi);
        });
    }
}
</code></pre>

#### Use the Summarizer
Let's say you are using a scheduled job to summarize the tickets on an interval.
We can simply type hint the interface in the job handle method and we'll get an instance
of OpenAITicketSummarizer.

<pre><code class="language-php">
// app/LLM/TicketSummarizer/SummarizeNewTicketsJob.php
class SummarizeNewTicketsJob
{
    public function handle(TicketSummarizerInterface $summarizer)
    {
        $newTickets = []; // some query result

        foreach ($newTickets as $ticket) {
            $summary = $summarizer->summarize($ticket->content);

            // store the summary somewhere
        }
    }
}
</code></pre>

#### Swapping OpenAI for Llama
The team got the Llama 3 instance running with an internal API to access it. It's time to write the Llama implementation and switch from Open AI. We don't want to delete Open AI because we need to time to find out if Llama is better for our use case or not and we might want to switch back to Open AI.

All we have to do is add the Llama summarizer class and update the service provider to return the Llama summarizer instead of the Open AI summarizer.

#### Create the LlamaTicketSummarizer
<pre><code class="language-php">
// app/LLM/TicketSummarizer/LlamaTicketSummarizer.php
class LlamaTicketSummarizer implements TicketSummarizerInterface
{
    private $llamaApi;

    public function __construct($llamaApi)
    {
        $this->llamaApi = $llamaApi
    }

    public function summarize(string $content): string
    {
        // send api request to internal llama api
        // return the string response
    }
}
</code></pre>

#### Update the Service Provider
<pre><code class="language-php">
// app/LLM/TicketSummarizer/TickerSummarizerProvider.php
class TicketSummarizerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(TicketSummarizerInterface::class, function ($app) {

            // Let's say OpenAiApi is a package we installed
            $llamaApi = $app->make('LlamaAPI');

            return new LlamaTicketSummarizer($llamaApi);
        });
    }
}
</code></pre>

And voila! You're now using Llama instead of Open AI to summarize the tickets. All you had to do was add a new class and change a few lines of code in your service provider. You could easily switch back to Open AI or to another LLM in the future with this set up. Your code is open for extension but closed for modification.

This is just one example of how Laravel inherently supports the Open/Closed Principle. There are many other features that demonstrate this principle, such as Middleware, Events & Listeners, Policies & Gates, and more. Each of these features enables you to extend your application’s functionality without altering existing code, fostering a clean, maintainable, and scalable codebase.

If you have questions about Open/Closed or feel that something wasn't clear about it in this article, please feel free to ask a question in the comments and I'll do my best to answer.