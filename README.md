PHP Bash script generator/runner.
=================================

[![Build Status](https://travis-ci.org/malc0mn/php-basher.svg?branch=master)](https://travis-ci.org/malc0mn/php-basher)
[![Latest Stable Version](https://poser.pugx.org/malc0mn/php-basher/v/stable)](https://packagist.org/packages/malc0mn/php-basher)
[![Total Downloads](https://poser.pugx.org/malc0mn/php-basher/downloads)](https://packagist.org/packages/malc0mn/php-basher)
[![Latest Unstable Version](https://poser.pugx.org/malc0mn/php-basher/v/unstable)](https://packagist.org/packages/malc0mn/php-basher)
[![License](https://poser.pugx.org/malc0mn/php-basher/license)](https://packagist.org/packages/malc0mn/php-basher)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a97a636f-4366-45c8-bcbc-94b004d66218/mini.png)](https://insight.sensiolabs.com/projects/a97a636f-4366-45c8-bcbc-94b004d66218)

## What! Why?

This is **by no means** a(n attempt at a) replacement tool for things like [Robo](https://github.com/consolidation/Robo).
I just thought it would be fun to create a **bash script** generator and executor
for PHP and it was a nice excuse to create an OO targeted PHP library again...

## Install using composer

Open a shell, `cd` to your poject and type:

```sh
composer require malc0mn/php-basher
```

or edit composer.json and add:

```json
{
    "require": {
        "malc0mn/php-basher": "~1.0"
    }
}
```

## Usage

An extremely simple example:

```php
<?php
use Basher\Tools\OSBase;

$base = new OSBase();

$base->set('-e', '-v')
    ->changeDir('/opt/approot')
    ->makeDir('build-new')
    ->delete('previous')
    ->renameIfExists('current', 'previous')
    ->link('build-new', 'current')
    ->set('-o pipefail')
;

echo (string)$base;
```
Would generate this output:

```bash
#!/bin/bash

set -e -v -o pipefail

cd /opt/approot
mkdir -p build-new
rm -f previous
if [ -d current -o -f current -o -L current ]; then mv -f current previous ; fi
ln -s build-new current

```

TODO: complete this readme :/
