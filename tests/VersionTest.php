<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use SteveGrunwell\SemVer\Exceptions\InvalidVersionException;
use SteveGrunwell\SemVer\Version;

/**
 * @covers \SteveGrunwell\SemVer\Version
 */
class VersionText extends TestCase
{
    /**
     * @test
     * @group Getters
     * @covers \SteveGrunwell\SemVer\Version::__toString
     */
    public function casting_to_a_string_should_call_getVersion()
    {
        $version = new Version;
        $version->setMajorVersion(1);
        $version->setMinorVersion(2);
        $version->setPatchVersion(3);

        $this->assertSame('1.2.3', (string) $version);
    }

    /**
     * @test
     * @group Getters
     * @covers \SteveGrunwell\SemVer\Version::getVersion
     */
    public function getVersion_should_retrieve_the_collapsed_string()
    {
        $version = new Version;
        $version->setMajorVersion(1);
        $version->setMinorVersion(2);
        $version->setPatchVersion(3);

        $this->assertSame('1.2.3', $version->getVersion());
    }

    /**
     * @test
     * @group Getters
     * @covers \SteveGrunwell\SemVer\Version::getVersion
     * @ticket https://github.com/stevegrunwell/semver-parser/issues/1
     */
    public function getVersion_should_include_the_prerelease_when_available()
    {
        $version = new Version;
        $version->setMajorVersion(1);
        $version->setMinorVersion(2);
        $version->setPatchVersion(3);
        $version->setPreReleaseVersion('alpha');

        $this->assertSame('1.2.3-alpha', $version->getVersion());
    }

    /**
     * @test
     * @group Getters
     * @covers \SteveGrunwell\SemVer\Version::getMajorVersion
     */
    public function getMajorVersion_should_return_the_major_version()
    {
        $version = new Version('1.2.3');

        $this->assertSame(1, $version->getMajorVersion());
    }

    /**
     * @test
     * @group Getters
     * @covers \SteveGrunwell\SemVer\Version::getMinorVersion
     */
    public function getMinorVersion_should_return_the_minor_version()
    {
        $version = new Version('1.2.3');

        $this->assertSame(2, $version->getMinorVersion());
    }

    /**
     * @test
     * @group Getters
     * @covers \SteveGrunwell\SemVer\Version::getPatchVersion
     */
    public function getPatchVersion_should_return_the_patch_version()
    {
        $version = new Version('1.2.3');

        $this->assertSame(3, $version->getPatchVersion());
    }

    /**
     * @test
     * @dataProvider provide_version_getters_and_setters
     * @group Getters
     */
    public function digit_getters_should_default_to_zero(string $getter)
    {
        $this->assertSame(0, (new Version)->{$getter}());
    }

    /**
     * @test
     * @group Getters
     * @covers \SteveGrunwell\SemVer\Version::getPreReleaseVersion
     * @ticket https://github.com/stevegrunwell/semver-parser/issues/1
     */
    public function getPreReleaseVersion_should_return_the_prerelease_version()
    {
        $version = new Version('1.2.3-alpha');

        $this->assertSame('alpha', $version->getPreReleaseVersion());
    }

    /**
     * @test
     * @group Getters
     * @covers \SteveGrunwell\SemVer\Version::getPreReleaseVersion
     * @ticket https://github.com/stevegrunwell/semver-parser/issues/1
     */
    public function getPreReleaseVersion_should_default_to_an_empty_string()
    {
        $version = new Version('1.2.3');

        $this->assertSame('', $version->getPreReleaseVersion());
    }

    /**
     * @test
     * @group Setters
     * @covers \SteveGrunwell\SemVer\Version::setMajorVersion
     */
    public function setMajorVersion_changes_the_major_version()
    {
        $version = new Version('1.2.3');
        $version->setMajorVersion(2);

        $this->assertSame('2.2.3', $version->getVersion());
    }

    /**
     * @test
     * @group Setters
     * @covers \SteveGrunwell\SemVer\Version::setMinorVersion
     */
    public function setMinorVersion_changes_the_minor_version()
    {
        $version = new Version('1.2.3');
        $version->setMinorVersion(3);

        $this->assertSame('1.3.3', $version->getVersion());
    }

    /**
     * @test
     * @group Setters
     * @covers \SteveGrunwell\SemVer\Version::setPatchVersion
     */
    public function setPatchVersion_changes_the_patch_version()
    {
        $version = new Version('1.2.3');
        $version->setPatchVersion(4);

        $this->assertSame('1.2.4', $version->getVersion());
    }

    /**
     * @test
     * @group Setters
     * @covers \SteveGrunwell\SemVer\Version::setPreReleaseVersion()
     * @ticket https://github.com/stevegrunwell/semver-parser/issues/1
     */
    public function setPreReleaseVersion_sets_the_prerelease_version()
    {
        $version = new Version('1.2.3-alpha');
        $version->setPreReleaseVersion('beta');

        $this->assertSame('1.2.3-beta', $version->getVersion());
    }

    /**
     * @test
     * @testdox Setters should not accept non-negative values
     * @dataProvider provide_version_getters_and_setters()
     * @group Setters
     */
    public function digit_setters_should_not_accept_non_negative_values(string $getter, string $setter)
    {
        $this->expectException(InvalidVersionException::class);

        (new Version)->{$setter}(-2);
    }

    /**
     * @test
     * @testdox Pre-release versions may only contain alphanumeric characters, hyphens, and dots
     * @dataProvider provide_invalid_identifiers()
     * @group Setters
     * @covers \SteveGrunwell\SemVer\Version::setPreReleaseVersion()
     * @ticket https://github.com/stevegrunwell/semver-parser/issues/1
     * @link https://semver.org/spec/v2.0.0.html#spec-item-9
     */
    public function prerelease_versions_should_be_validated(string $identifier)
    {
        $this->expectException(InvalidVersionException::class);

        (new Version)->setPreReleaseVersion($identifier);
    }

    /**
     * @test
     * @group Setters
     * @dataProvider provide_version_getters_and_setters()
     */
    public function digit_values_can_be_incremented(string $getter, string $setter)
    {
        $version = new Version;
        $method  = 'increment' . substr($setter, 3);
        $version->{$setter}(1);
        $version->{$method}();

        $this->assertSame(2, $version->{$getter}());
    }

    /**
     * @test
     * @group Setters
     * @covers \SteveGrunwell\SemVer\Version::incrementMajorVersion()
     * @link https://semver.org/spec/v2.0.0.html#spec-item-8
     */
    public function incrementMajorVersion_should_reset_minor_and_patch_versions_to_zero()
    {
        $version = new Version('1.2.3');

        $version->incrementMajorVersion();

        $this->assertSame(0, $version->getMinorVersion());
        $this->assertSame(0, $version->getPatchVersion());
    }

    /**
     * @test
     * @group Setters
     * @covers \SteveGrunwell\SemVer\Version::incrementMajorVersion()
     * @link https://semver.org/spec/v2.0.0.html#spec-item-7
     */
    public function incrementMinorVersion_should_reset_patch_versions_to_zero()
    {
        $version = new Version('1.2.3');

        $version->incrementMajorVersion();

        $this->assertSame(0, $version->getPatchVersion());
    }

    /**
     * @test
     * @group Setters
     * @dataProvider provide_version_getters_and_setters()
     */
    public function digit_values_can_be_decremented(string $getter, string $setter)
    {
        $version = new Version;
        $method  = 'decrement' . substr($setter, 3);
        $version->{$setter}(2);
        $version->{$method}();

        $this->assertSame(1, $version->{$getter}());
    }

    /**
     * @test
     * @group Setters
     * @dataProvider provide_version_getters_and_setters()
     */
    public function digit_values_cannot_be_decremented_below_zero(string $getter, string $setter)
    {
        $version = new Version;
        $method  = 'decrement' . substr($setter, 3);
        $version->{$setter}(0);

        $this->expectException(InvalidVersionException::class);

        $version->{$method}();
    }

    /**
     * @test
     * @dataProvider provide_control_versions()
     */
    public function all_examples_from_the_specification_should_pass(string $version, int $major, int $minor, int $patch, string $prerelease)
    {
        $version = new Version($version);

        $this->assertSame($major, $version->getMajorVersion());
        $this->assertSame($minor, $version->getMinorVersion());
        $this->assertSame($patch, $version->getPatchVersion());
        $this->assertSame($prerelease, $version->getPreReleaseVersion());
    }

    /**
     * As a control, include examples given in the specification.
     */
    public function provide_control_versions(): array
    {
        return [
            // https://semver.org/spec/v2.0.0.html#spec-item-9
            ['1.0.0-alpha', 1, 0, 0, 'alpha' ],
            ['1.0.0-alpha.1', 1, 0, 0, 'alpha.1'],
            ['1.0.0-0.3.7', 1, 0, 0, '0.3.7'],
            ['1.0.0-x.7.z.92', 1, 0, 0, 'x.7.z.92'],

            // If digits are missing, treat them as zeros.
            ['1.0', 1, 0, 0, ''],
            ['1', 1, 0, 0, ''],
            ['1-alpha', 1, 0, 0, 'alpha'],
            ['1-alpha.1', 1, 0, 0, 'alpha.1']
        ];
    }

    /**
     * Return all of the available version setter methods.
     */
    public function provide_version_getters_and_setters(): array
    {
        return [
            'Major' => ['getMajorVersion', 'setMajorVersion'],
            'Minor' => ['getMinorVersion', 'setMinorVersion'],
            'Patch' => ['getPatchVersion', 'setPatchVersion'],
        ];
    }

    /**
     * Provide invalid pre-release version identifiers.
     */
    public function provide_invalid_identifiers(): array
    {
        return [
            'Whitespace'  => ['alpha v1'],
            'Underscores' => ['alpha_v1'],
        ];
    }
}
