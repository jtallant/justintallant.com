categories: [Programming]
title: Combining PHPSpec and PHPUnit Code Coverage in CircleCI
---

I really like using [PHPSpec](http://www.phpspec.net/en/stable/) for unit testing but for integration tests we need to use PHPUnit or something similar. I prefer having code coverage combined for my unit and integration tests but to accomplish this we have to combine our phpunit coverage with our phpspec coverage. Luckily we can accomplish this with the [phpcov](https://github.com/sebastianbergmann/phpcov) package.

Below are configurations I use to accomplish this in the build process with CircleCI. Note that I like creating a separate config file for running phpspec and generating coverage. You don't want to generate coverage every time you run your tests, it's too slow. To avoid this, you can create a separate config file and pass it to <code class="c">phspec run</code> like so: <pre class="c">phpspec run --config phpspec-coverage.yml</pre>

See below for the actual content of the config file.

We can combine coverage of phpspec and phpunit manually over CLI. All you have to do is generate the coverage in .cov format for each individually and then run phpcov merge on the two files to merge them into a clover.xml file.

<pre><code class="language-bash">
\# Generate PHPUnit .cov file
./vendor/bin/phpunit --coverage-php build/php/phpunit.cov

\# Generate phpspec .cov file
\# See config file below for how the phpspec command works
./vendor/bin/phpspec run --config phpspec-coverage.yml

\# Use phpcov to merge the files
./vendor/bin/phpcov merge ./build/php --clover ./build/clover.xml
</pre></code>

That's the basic idea. You can reference the config files below if you want this to be part of your build process. I like this because it allows your code climate coverage statistics to include both and it's also nice just looking at HTML coverage reports to see where you aren't covered.

Notice that I create composer scripts to name the commands just so it's more clear as to what is going on when you are reading the CircleCI config file.

## Overview
1. Require packages
    * phpunit
    * phpspec
    * phpcov
    * phpspec-code-coverage
2. Create phpspec-coverage.yml
3. Define composer scripts
4. Configure circleci
5. Make sure circle-ci is using a "user" key so it can access all repositories in your github account
5. Make sure you circle-ci docker image includes xdebug

## composer packages
<pre><code class="language-json">
"require-dev": {
    "friends-of-phpspec/phpspec-code-coverage": "^4.0@dev",
    "mockery/mockery": "^1.0@dev",
    "phpspec/phpspec": "^5.1@dev",
    "phpunit/phpcov": "^6.0@dev",
    "phpunit/phpunit": "~8.1",
    "symfony/var-dumper": "^4.3@dev"
},
</code></pre>

## composer.json scripts
<pre><code class="language-json">
"scripts": {
    "test": "phpspec run --format=pretty && ./vendor/bin/phpunit",
    "unit": "./vendor/bin/phpunit",
    "spec": "phpspec run --format=pretty",
    "phpunit-html": "./vendor/bin/phpunit --coverage-html build/phpunit-coverage",
    "phpunit-coverage": "./vendor/bin/phpunit --coverage-php build/php/phpunit.cov",
    "phpspec-html": "./vendor/bin/phpspec run --config phpspec-html.yml",
    "phpspec-coverage": "./vendor/bin/phpspec run --config phpspec-coverage.yml",
    "merge-coverage": "./vendor/bin/phpcov merge ./build/php --clover ./build/clover.xml",
    "pub-coverage": "./vendor/bin/phpcov merge ./build/php --clover clover.xml"
},
</code></pre>

## phpspec-coverage.yml
<pre><code class="language-yaml">
extensions:
  FriendsOfPhpSpec\PhpSpec\CodeCoverage\CodeCoverageExtension:
    format:
      - html
      - php
    output:
      html: build/phpspec-coverage
      php: build/php/phpspec.cov
</code></pre>

## .circleci/config.yml
<pre><code class="language-yaml">
version: 2

jobs:
  build:
    environment:
      CC_TEST_REPORTER_ID: XXXXXXXXXX
    docker:
      - image: circleci/php:7.2.17-cli-stretch
    working_directory: ~/project-name
    steps:
      - checkout
      - restore_cache: # special step to restore the dependency cache if `composer.lock` does not change
          keys:
            - composer-v1-{{ checksum "composer.lock" }}
            # fallback to using the latest cache if no exact match is found (See https://circleci.com/docs/2.0/caching/)
            - composer-v1-
      - run: composer install -n --prefer-dist
      - save_cache: # special step to save the dependency cache with the `composer.lock` cache key template
          key: composer-v1-{{ checksum "composer.lock" }}
          paths:
            - vendor
      - run: curl -L https://codeclimate.com/downloads/test-reporter/test-reporter-latest-linux-amd64 > ./cc-test-reporter
      - run: chmod +x ./cc-test-reporter
      - run: sudo mkdir -p $CIRCLE_TEST_REPORTS/phpunit
      - run: ./cc-test-reporter before-build
      - run: composer phpunit-coverage
      - run: composer phpspec-coverage
      - run: composer pub-coverage
      - run: ./cc-test-reporter after-build -t clover --exit-code $?
</code></pre>