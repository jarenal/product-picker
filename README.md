#Welcome to Product Picker!

Product picker is an small script that will find the best and faster way for to pick your products readed from an input CSV file.

## REQUIREMENTS

This package has the next dependencies:

- [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit): (Optional) Used for to run the unitary tests. (Requires PHP >= 5.6.0)

## INSTALL

Download and uncompress the package in your computer and then execute:

```bash
	$ composer install
```

NOTICE: You need to have composer installed in your system, for more information follow the next link [How install composer?](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## USAGE

The script is located at /bin/product-picker.php, you can executed using the next command:

```bash
	$ ./bin/product-picker.php
```

NOTICE: Be sure that the script has execution permissions.

If you call the script without parameters will show you the help screen.

Below you have a briefing of the help shown by the script:

	Usage:

		product-picker.php [options]

	This script will find the optimal route through the warehouse for to pick the products.

	Optional arguments:

	  -h, --help          Show this help message.
	  -i, --input         Input file path (CSV format).
	  -o, --output        Output file path (CSV format).

	Examples:

		Using short-name options:

	        $ ./product-picker.php -i input.csv -o output.csv

	    Using long-name options:

	        $ ./product-picker.php --input=input.csv --output=output.csv

## COMPILATION

The script is generated using a compiler, that allow us run the unitary tests against the classes or improve the script with new implementations.

For to execute the compiler you will need call the next script without parameters.

```bash
	$ ./src/Jarenal/bin/compiler.php
```

The compiler will saves the new script at 'bin/product-picker.php' in the root of your project.

##TESTS

If you want run the tests, you can use the next command:

```bash
	$ phpunit --bootstrap vendor/autoload.php src/Jarenal/Tests
```

NOTE: Before run the tests you will need install:

- [Install PHPUNIT globally](https://phpunit.de/manual/current/en/installation.html)
- [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit) package through composer.













