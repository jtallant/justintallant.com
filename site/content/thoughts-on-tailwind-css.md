title: Thoughts on Tailwind CSS
date: "2024-06-03"
---
At first, I thought Tailwind CSS was awesome, and in many ways, it still is. It provides a quick and easy way to style components without having to write custom CSS. The utility-first approach can be very efficient, especially for rapid prototyping or when working on smaller projects.

However, after using Tailwind for some time, I've decided to switch back to traditional CSS for most of my projects. One of the main reasons for this decision is readability and speed. Over time, I noticed Tailwind actually slowing things down instead of speeding them up. With Tailwind, the HTML can become cluttered with a slew of class names, making it harder to understand the structure and purpose of each component at a glance. Clean markup is more important now than ever due to the heavy use of reactive JS libraries like Vue and React. You need to be able to glance over your HTML and understand what's going on.

Additionally, with the rise of AI-powered coding assistants like Cursor (ChatGPT, Copilot, etc.), the time-saving benefits of Tailwind have diminished for me. I can quickly create vanilla HTML markup and then use Cursor's Copilot/GPT integration to generate advanced styling. After that, I simply assign semantic class names to my easily readable HTML and move on.

Speed isn't the only factor that drew me to Tailwind. Tailwind has had fantastic design out of the gate in my opinion. Before Tailwind, I was already well-versed in CSS. If someone gave me a design document, I could match it almost pixel for pixel. But if I didn't have a design document, I couldn't easily come up with well-designed interfaces. Using Tailwind for some time actually improved my design skills.

Now that I've used Tailwind for a while and paid more attention to what makes a good design, I'm able to create solid designs without it. I've gained confidence in my ability to design interfaces using traditional CSS techniques.

Having said all of that, I still think Tailwind is great. Tailwind's ability to create reusable, styled components is incredibly useful for sharing markup between projects or with other developers. It's convenient to copy a chunk of HTML and paste it into your project, knowing that it will look the same without any additional setup.

In the end, it comes down to personal preference and the specific needs of each project. For me, I feel less confused and find it easier to work with my markup when it comes to page layouts, templating, and reactive JavaScript using traditional CSS. Now that coding assistants are so advanced, I believe that the ability to quickly understand and reason about my applications is more important than ever.

While I certainly appreciate Tailwind and don't wish for it to go away, I've found that it's not always the best fit for my current workflow. Sometimes, the simplicity of writing traditional CSS with sass outweighs the benefits of using a CSS framework like Tailwind.
