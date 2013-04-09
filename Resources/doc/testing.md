Testing the Bundle
==================

## Install the Environment

In order to run the UnitTests of this bundle, clone it first

    $> git clone git://github.com/1up-lab/OneupUploaderBundle.git

After the cloning process, install all vendors by running the corresponding composer command.

    $> php composer.phar udpate --dev

## Run UnitTests
You can run the unit tests by simply performing the follwowing command.

    $> phpunit

## Testing Code Coverage
PHPUnit comes bundles with a handy feature to test the code coverage of a project. I recommend using the following configuration to enable the creation of code coverage reports in the `log` directory in the root of this bundle. This directory is gitignored by default.

Copy the `phpunit.xml.dist` to `phpunit.xml` and use this configuration.

```xml
<?xml version="1.0" encoding="UTF-8"?>

<phpunit bootstrap="./Tests/bootstrap.php" colors="true">

    <testsuites>
        <testsuite name="OneupUploaderBundle test suite">
            <directory suffix="Test.php">./Tests</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist>
            <directory>./</directory>
            <exclude>
                <directory>./Command</directory>
                <directory>./DependencyInjection</directory>
                <directory>./Event</directory>
                <directory>./Resources</directory>
                <directory>./Tests</directory>
                <directory>./vendor</directory>
                <directory>./OneupUploaderBundle.php</directory>
            </exclude>
        </whitelist>
    </filter>

    <logging>
        <log type="coverage-html" target="./log/codeCoverage" charset="UTF-8" yui="true" highlight="true"
            lowUpperBound="50" highLowerBound="80"/>
    </logging>

</phpunit>
```

The directories `Command`, `DependencyInjection` and `Event` are excluded from the code coverage report, as these directories contain files that don't necessarily need to be unit tested.

Run the test suite and generate reports by running:

    $> phpunit