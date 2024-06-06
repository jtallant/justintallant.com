title: Why I'm Using Skimpy CMS for my Blog
date: "2024-06-04"
---
This blog is created with my own open source CMS called Skimpy. I think web development has a tendency towards complexity and sometimes we get carried away. Skimpy takes things back to the basics.

#### For my own user experience when blogging I wanted a few things
1. The ability to write my blog posts in markdown
    * This includes writing the metadata (date, title) in markdown (often referred to as front matter).
    * This also means everything is in version control. Your files are the content.
2. The ability to query my file content using SQL
    * In short, Skimpy caches your files to an sqlite DB.
    * This means you only work with files but you can still query everything using Doctrine.
    * This is huge IMO and what sets Skimpy apart from everything else.
2. Automatic categorization using a directory structure
    * This also allows you to easily find things in your IDE.
3. Twig templating
    * I know Twig isn't as well known to the Laravel as community as it is the Symfony folks but IMO, it's still the best and easiest templating system in PHP. I still wish Taylor would have just adopted it right away instead of creating Blade. Sure would have made things easier for all PHP developers, everyone would just be using Twig.
4. To NOT have to generate my blog to HTML like you do with Jekyll.
    * Static generators or just a PITA in my opinion.
    * The only thing they really accomplish for you that matters is the ability to host your site on github pages or something similar
    * That's one drawback to Skimpy, it's not a static site. You work with it like it is but you don't deploy it like one.
    * You still need to deploy it to a server that runs PHP.
    * For me the ease of use is worth the extra time to set up automatic deployment on a server.
    * That means I also pay for hosting but I host other PHP sites anyway so there isn't a difference for me in cost.

