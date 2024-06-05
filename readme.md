<p align="center">
    <a href="https://packagist.org/packages/skimpy/cms"><img src="https://poser.pugx.org/skimpy/cms/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/skimpy/cms"><img src="https://poser.pugx.org/skimpy/cms/license.svg" alt="License"></a>
</p>


## About Skimpy

Skimpy is a file based CMS that is written with and runs on PHP (Lumen). It is NOT a static site generator. Generators are cumbersome tools. Skimpy is easy to use. You just create a file, and there it is! Skimpy is so easy to use that it might be the easiest CMS/blogging tool that you've ever tried. Don't believe me? Give it a shot!

## Documentation

<ul>
    <li>
        <a href="#introduction">Introduction</a>
    </li>
    <li>
        <a href="#installation">Installation</a>
    </li>
    <li><a href="#creating-a-blog-post">Creating a Blog Post</a></li>
    <li><a href="#adding-front-matter">Adding Front Matter</a></li>
    <li><a href="#creating-a-page">Creating a Page</a></li>
    <li><a href="#categorizing-content">Categorizing/Tagging Content</a></li>
    <li><a href="#creating-page-types">Creating Page Types</a></li>
    <li><a href="#index-pages">Index Pages</a></li>
    <li><a href="#uri-mapping--template-variables">URI Mapping & Template Variables</a></li>
    <li><a href="#template-hierarchy">Template Hierarchy</a></li>
    <li><a href="#custom-templates">Custom Templates</a></li>
    <li><a href="#how-does-skimpy-work">How does Skimpy work?</a></li>
</ul>

## Introduction
<p>
    Skimpy is a simple file based CMS that can be used to make a website or blog. Skimpy is built for developers,
    though anyone who can create files and put a PHP site on the internet can use it. Skimpy aims to be simple and easy to use.
</p>

# Installation

#### Server Requirements
<ul>
    <li>PHP >= 8.0.2</li>
    <li>OpenSSL PHP Extension</li>
    <li>PDO PHP Extension</li>
    <li>Mbstring PHP Extension</li>
</ul>

<h4>Installing Skimpy</h4>
<ol style="margin-bottom: 0px;">
    <li><code>composer create-project --prefer-dist skimpy/cms skimpy</code></li>
    <li><code>cd path/to/skimpy</code></li>
    <li><code>cp .env.example .env</code></li>
    <li>Update the .env file to match your preferences/info</li>
    <li><code>php -S localhost:4000 -t public</code></li>
    <li>Visit http://localhost:4000</li>
</ol>

<h2>Creating a Blog Post</h2>

<ol>
    <li>Create a new markdown file called <code>test-post.md</code> inside the <code>site/content</code> directory.</li>
    <li>Visit <code>http://localhost:4000/test-post</code> and voila!</li>
    <li>Add a markdown header to the file <code># Test Header</code> and refresh. You'll see an h1 tag that reads "Test Header".</li>
</ol>

<h4>How URIs are determined</h4>
<p>
    In the example above where we created "test-post", you can see that Skimpy uses the filename as the URI to the post.
    If you put the file inside a directory "products" and name the file "widget.md". The URI to the file will be <code>/products/widget</code>
</p>

```
path = path/to/skimpy/site/content/products/widget.md
URL = http://localhost:4000/products/widget</pre>
```

<h2>Adding Front Matter</h2>

<p>
    You can customize certain properties of an entry using front matter key values. All front matter is optional and Skimpy will use sensible defaults.
</p>

<p>Always use exactly three hyphens "---" to separate front matter from content. Otherwise, you'll get an exception or your file won't parse correctly.</p>

<p>Keep in mind that using three hypens "---" on their own line, or at the end of the file without a new line, will cause Skimpy to assume it is the front matter separator whether that is what you intend or not.</p>

<table>
    <caption>Native Front Matter Keys</caption>
    <thead>
        <tr>
            <th>Key</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>title</td>
            <td>Custom title if different from the filename</td>
        </tr>
        <tr>
            <td>date</td>
            <td>The published date of the content. Always use PHP Y-m-d or Y-m-d H:i:s format. DATES MUST BE QUOTED "2020-01-22"</td>
        </tr>
        <tr>
            <td>seoTitle</td>
            <td>The content to put in the head &lt;title&gt; tag</td>
        </tr>
        <tr>
            <td>description</td>
            <td>The SEO meta description for this page (if any)</td>
        </tr>
        <tr>
            <td>template</td>
            <td>The name of the template to use if not using whichever template matches the convention for this content type</td>
        </tr>
        <tr>
            <td>categories</td>
            <td>The categories the content belongs to</td>
        </tr>
        <tr>
            <td>tags</td>
            <td>The tags to assign to the content</td>
        </tr>
    </tbody>
</table>

<h4>Front Matter Metadata</h4>
<p>Any key/value you put in front matter that isn't built in will be stored as metadata.</p>
<p>You can access any custom front matter key values in your templates.</p>
<p>The "restaurants" key in the example file below is an example of metadata.</p>

<h4>Accessing FrontMatter Metadata in Templates</h4>
<p>Call the "meta" method on the entry object and pass in the key you want.</p>
<p><code>{{ "{{ entry.meta('restaurants') }}"}}</code></p>

<h4>Example Content File With Front Matter</h4>

```
title: My 2019 Trip to Germany
date: "2019-08-27"
description: The SEO meta description (if any) goes here
seoTitle: Custom SEO title here
categories: [Vacation]
tags: [Personal Growth]
restaurants: [Kin Dee, Markthalle Neun]
---
The content for the post goes here...
```

<h2>Creating a Page</h2>

<p>
    Skimpy sites don't have to be blogs at all but it does make blogging easy if that's what you are doing.
    Before I explain "pages" in Skimpy, let's first define what a page is. Both pages and posts are just files that hold
    content for display when someone hits the matching URI. The only difference between a "page" and a "post" is that people
    don't typically display an index of pages like they do blog posts. Pages are not usually listed on the home screen with "excerpts" like
    blog posts and pages aren't really meant to be categorized or tagged.
</p>
<p>
    So if you would like to create an "entry" on your site
    that doesn't show up in your default blog feed, then you want to create a page. This is accomplished by creating a subdirectory under
    the content directory and placing any files that you want to be a "page" inside that directory. You can call the directory anything
    you want but keep in mind the directory name will be part of the URI to access the page.
</p>
<p class="mb-0">
    You may also exclude root entries from displaying on the home page or an index by manually changing the content type via the Front Matter
    "type" key. This essentially is the same as placing the file in a subdirectory. Any file inside a subdirectory has a "type" property with
    a value matching the name of the parent directory. Try and avoid using the "type" key if you can. Sites that follow the conventions
    will be more well organized and easier to navigate. You can always just make a "pages" directory and put your pages there. Having a URI
    segment "pages" precede the page URI really isn't a big deal.
</p>

<h2>Taxonomies - Categorizing & Tagging Content</h2>

<p>
Content can be categorized or tagged. Categories and tags are referred to as "taxonomies" (like in WordPress).
Taxonomies are a way of classifying content. There are already two predefined taxonomies in the default
Skimpy installation. And you guessed it, those taxonomies are categories and tags. If you wanted to add your own
taxonomy, you would just copy the <code>categories.yaml</code> file and change the values.
</p>

<p>
Taxonomies have "terms". In the case of the "categories" taxonomy, the terms are the actual categories.
You add new categories by adding a new array to the terms key in the <code>content/taxonomies/categories.yaml</code>
file. You simply provide a name for the category and a slug to be used in the URI.
</p>

<h4>Public Terms Route</h4>
<p>
    By default, Skimpy will list links to the terms (category names)
    you have defined in the <code>categories.yaml</code> file, when you visit
    <code>/categories</code>. If you want to turn this off for a particular taxonomy
    (like categories) then you should set the <code>has_public_terms_route</code>
    yaml key to <code>false</code> in <code>categories.yaml</code>.
</p>

<h4>Assigning Categories/Tags</h4>
<p>
You simply add the key <code>categories</code> to the YAML front matter of any content file and provide an array of category names. Use the actual "name" NOT the slug.
</p>
<p>
Don't forget the Front Matter separator! "---"
</p>

```
categories: [Web Development]
tags: [Tag 1]
date: "2020-01-21"
---
Your post content here...
```

<h2>Creating Page Types</h2>

<p>
Let's say you want to add a page for every person on your team and you want to list those people
on your "team" page.
</p>

<ol class="mb-0">
    <li>Create folder <code>content/team</code></li>
    <li>Create file <code>content/team/kevin-rose.md</code></li>
    <li>Create file <code>content/team/tim-ferriss.md</code></li>
    <li>Enable a "team index" by creating <code>content/team/index.md</code></li>
    <li>
        The following URLs are now valid<br />
        <ul>
            <li><code>example.com/team</code> - Lists links to any file under the "team" directory</li>
            <li><code>example.com/team/kevin-rose</code></li>
            <li><code>example.com/team/tim-ferriss</code></li>
        </ul>
    </li>
</ol>

<h2>Index Pages</h2>

<p>Index pages are basically an archive of content files inside of a subdirectory of content.</p>

<p>See the example for <a href="#creating-page-types">creating page types</a></p>

<p>
By default, there is no index for files inside a subfolder of the content directory.
To turn the index uri on, in other words, to make the subfolder name a valid URI on your
website, you just create a file inside the subdirectory called <code>index.md</code>
</p>

<h2>URI Mapping & Template Variables</h2>

<p>
There are three types of "entities" in Skimpy - <code>entry</code>, <code>taxonomy</code>, and <code>term</code>.
The current URI determines what type of entity is queried and what the template variable names are.
You can determine what file is being displayed just by looking at the URI. <b>The file structure
maps directly to the current URI</b>. Just keep in mind when you create a taxonomy file, you are
creating a URL that matches the name of that file. When you create a directory inside content and you place
an index.md file in it, you are creating a URL that matches the path to that directory.
</p>

<table>
    <caption>URI Mapping</caption>
    <thead>
        <tr>
            <th>When URI Matches</th>
            <th>You'll See</th>
            <th>Template Variable</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>the name of a content file</td>
            <td>The files markdown content</td>
            <td><code>entry</code></td>
        </tr>
        <tr>
            <td>the name of a taxonomy file (like "categories")</td>
            <td>A listing of links to the terms registered to the taxonomy</td>
            <td><code>taxonomy</code></td>
        </tr>
        <tr>
            <td>the name of a term (like a category name)</td>
            <td>A listing of links to entries with that term assigned in their Front Matter</td>
            <td><code>term</code></td>
        </tr>
    </tbody>
</table>

<h2>Template Hierarchy</h2>

<div class="alert alert-info">
    <p>
        Skimpy uses <a href="https://twig.symfony.com/doc/2.x/" target="_blank">Twig</a> for templating. Please see the
        <a href="https://twig.symfony.com/doc/2.x/" target="_blank">Twig Docs</a> to discover all the power of Twig.
    </p>
</div>

<p>Which template is used depends on the type of entity (entry, taxonomy, term) being displayed. You can manually set the template with the <code>template</code> front matter key<p>

<ol class="mb-0">
    <li>FrontMatter key <code>template: your-template-name</code></li>
    <li>The parent folder name if the file is in a subdirectory of content</li>
    <li>If the file is an index (index.md), the index template.</li>
    <li>The entity type (entry = entry.twig, taxonomy = taxonomy.twig, term = term.twig)</li>
</ol>

<h2>Custom Templates</h2>

<div class="alert alert-info">
    <p>
        Skimpy uses <a href="https://twig.symfony.com/doc/2.x/" target="_blank">Twig</a> for templating. Please see the
        <a href="https://twig.symfony.com/doc/2.x/" target="_blank">Twig Docs</a> to discover all the power of Twig.
    </p>
</div>

<p>
    Skimpy <a href="#template-hierarchy">has some conventions</a> for deciding what template to use. You can override the conventions
    by adding a <code>template</code> key to the front matter of a content file.
</p>

<h4>Using a custom template</h4>
<ol class="mb-0">
    <li>Create the template <code>site/templates/my-custom-template.twig</code></li>
    <li>
        Open or create a content file and set the template in the Front Matter.<br />
        <code>template: my-custom-template</code>
    </li>
</ol>

<h2>Deployment</h2>
<p>
    Deploying Skimpy to production is easy. Just make sure your server meets the <a href="#server-requirements">server requirements</a>
    and create a virtual host with the document root set to the skimpy public folder.
</p>

<h4>Auto Rebuild</h4>
<p>
    What is auto rebuild? Auto rebuild refers to Skimpy scanning your content folder and updating the SQLite DB on every request to your website.
    This makes development quick and easy. You don't have to worry about "generating" your website. You can turn this feature on or off
    in your local or production environment. I wouldn't really worry about it too much because it's not going to make much of a difference. That being
    said I will share my preferred way of deploying with Skimpy.
</p>

<h4>How I Deploy Skimpy</h4>
<ol class="mb-0">
    <li>
        <p>I track my SQLite DB with Git so it gets automatically deployed when I push to master. This allows my Skimpy sites to be updated to latest when I push to master without the production sites having to rebuild the database on each request</p>
    </li>
    <li>
        I set <code>AUTO_REBUILD=false</code> in my <code>.env</code> file <b>on the production site</b> so the database won't be rebuilt on every request.
    </li>
</ol>

<h2>How does Skimpy work?</h2>
<p>
    When a request hits your website, Skimpy scans all of the files in your <code>site/content</code> directory, converts them to
    <a href="https://www.doctrine-project.org/" target="_blank">Doctrine</a> entities, and shoves them into an sqlite database. You don't have to pay any
    attention to the database at all if you don't want. The reason Skimpy converts your content into database records is so that it can take
    advantage of all of the power of doctrine and SQL in general. The database component in Skimpy is used more like a cache.
    You should never be editing your database directly as Skimpy will just wipe out any changes you made automatically the next time your website receives a request.
</p>
<p class="mb-0">
    Skimpy uses Doctrine and a database for several reasons. The primary reason for the DB is so that you don't have to "generate" your actual
    website everytime you make changes to your content, or run a "watch" command that generates the changes when you save a file. Doing
    things the "generater way" is just nonsense. Generating adds complexity to creating a website or blog. Writing and adding content should be mindless
    and that's what Skimpy sets out to accomplish.
</p>

## License

Skimpy CMS is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).