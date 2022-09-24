# Cw.LearnBear

## Installation

```bash
git clone https://github.com/clap-and-whistle/learn-bearsunday.git
cd learn-bearsunday
composer install
```

## Usage

### Invoke Request

    composer page get /

### Available Commands

    composer serve             // start builtin server
    composer test              // run unit test
    composer tests             // test and quality checks
    composer twig-clean        // Clear twig cache files
    composer coverage          // test coverage
    composer cs-fix            // fix the coding standard
    composer doc               // generate API document
    composer run-script --list // list all commands

## open issue

```
#########################################
### First, delete temporary files

$ rm -rf ./var/tmp/*


#########################################
### Next, run the first phpunit

$ composer test
> ./vendor/bin/phpunit
PHPUnit 9.5.24 #StandWithUkraine

....F..EEEE...F...........                                        26 / 26 (100%)

Time: 00:04.648, Memory: 34.00 MB

There were 4 errors:


#########################################
### Then, run phpunit for the second time

$ composer test
> ./vendor/bin/phpunit
PHPUnit 9.5.24 #StandWithUkraine

..........................                                        26 / 26 (100%)

Time: 00:04.260, Memory: 36.00 MB

OK (26 tests, 99 assertions)
```

Why is it that the first one fails but succeeds from the subsequent second run?
    
## Links

- A memoir of the creation of this project on the author's blog
  - https://zenn.dev/clap_n_whistle/articles/b730c5faab7d58
- BEAR.Sunday
  - http://bearsunday.github.io/
