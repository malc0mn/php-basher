<?php

namespace Basher\Tests;

use Basher\Tools\OSBase;
use PHPUnit\Framework\TestCase;

class OSBaseTest extends TestCase
{
    /**
     * @var OSBase
     */
    protected $base;

    protected function setUp(): void
    {
        $this->base = new OSBase();
    }

    public function testChangeDir()
    {
        $this->base->changeDir('some/path/to/test');

        $this->assertEquals('cd some/path/to/test', $this->base->getStacked());
    }

    public function testDelete()
    {
        $this->base->delete('path/to/stuff/we-dont-need', false);

        $this->assertEquals('rm path/to/stuff/we-dont-need', $this->base->getStacked());
    }

    public function testDeleteForce()
    {
        $this->base->delete('path/to/stuff/we-dont-need');

        $this->assertEquals('rm -f path/to/stuff/we-dont-need', $this->base->getStacked());
    }

    public function testDeleteRecursive()
    {
        $this->base->delete('path/to/stuff/we-dont-need', false, true);

        $this->assertEquals('rm -r path/to/stuff/we-dont-need', $this->base->getStacked());
    }

    public function testDeleteForceRecursive()
    {
        $this->base->delete('path/to/stuff/we-dont-need', true, true);

        $this->assertEquals('rm -f -r path/to/stuff/we-dont-need', $this->base->getStacked());
    }

    public function testLinkSymbolic()
    {
        $this->base->link('real/goes/first', 'link/to/here');

        $this->assertEquals('ln -s real/goes/first link/to/here', $this->base->getStacked());
    }

    public function testLinkHard()
    {
        $this->base->link('real/goes/first', 'link/to/here', false);

        $this->assertEquals('ln real/goes/first link/to/here', $this->base->getStacked());
    }

    // TODO: test link() allowFail option

    public function testMakeDir()
    {
        $this->base->makeDir('/opt/approot/build', false);

        $this->assertEquals('mkdir /opt/approot/build', $this->base->getStacked());
    }

    public function testMakeDirRecursive()
    {
        $this->base->makeDir('/opt/approot/build');

        $this->assertEquals('mkdir -p /opt/approot/build', $this->base->getStacked());
    }

    public function testMove()
    {
        $this->base->move('src/old-name.txt', 'dst/new-name.txt', false);

        $this->assertEquals('mv src/old-name.txt dst/new-name.txt', $this->base->getStacked());
    }

    public function testMoveForce()
    {
        $this->base->move('src/old-name.txt', 'dst/new-name.txt');

        $this->assertEquals('mv -f src/old-name.txt dst/new-name.txt', $this->base->getStacked());
    }

    // TODO: test move() allowFail option

    public function testMoveIfExists()
    {
        $this->base->moveIfExists('src/old-name.txt', 'dst/new-name.txt', false);

        $this->assertEquals('if [ -d src/old-name.txt -o -f src/old-name.txt -o -L src/old-name.txt ]; then mv src/old-name.txt dst/new-name.txt ; fi', $this->base->getStacked());
    }

    public function testMoveIfExistsForce()
    {
        $this->base->moveIfExists('src/old-name.txt', 'dst/new-name.txt');

        $this->assertEquals('if [ -d src/old-name.txt -o -f src/old-name.txt -o -L src/old-name.txt ]; then mv -f src/old-name.txt dst/new-name.txt ; fi', $this->base->getStacked());
    }

    public function testMoveIfExistsFail()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Unknown filetype test!');

        $this->base->moveIfExists('src/old-name.txt', 'dst/new-name.txt', false, ['test']);
    }

    public function testRename()
    {
        $this->base->rename('old-name.txt', 'new-name.txt', false);

        $this->assertEquals('mv old-name.txt new-name.txt', $this->base->getStacked());
    }

    public function testRenameForce()
    {
        $this->base->rename('old-name.txt', 'new-name.txt');

        $this->assertEquals('mv -f old-name.txt new-name.txt', $this->base->getStacked());
    }

    // TODO: test rename() allowFail option


    public function testRenameIfExists()
    {
        $this->base->renameIfExists('old-name.txt', 'new-name.txt', false);

        $this->assertEquals('if [ -d old-name.txt -o -f old-name.txt -o -L old-name.txt ]; then mv old-name.txt new-name.txt ; fi', $this->base->getStacked());
    }

    public function testRenameIfExistsForce()
    {
        $this->base->renameIfExists('old-name.txt', 'new-name.txt');

        $this->assertEquals('if [ -d old-name.txt -o -f old-name.txt -o -L old-name.txt ]; then mv -f old-name.txt new-name.txt ; fi', $this->base->getStacked());
    }

    public function testRenameIfExistsFail()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Unknown filetype test!');

        $this->base->moveIfExists('old-name.txt', 'new-name.txt', false, ['test']);
    }

    public function testSetFacl()
    {
        $this->base->setFacl('www/var', 'www-data', 'rwX', false);
        $this->assertEquals('setfacl -m u:"www-data":rwX www/var', $this->base->getStacked());
    }

    public function testSetFaclRecursive()
    {
        $this->base->setFacl('www/var', 'www-data');
        $this->assertEquals('setfacl -R -m u:"www-data":rwX www/var', $this->base->getStacked());
    }

    public function testSetFaclRecursiveDefault()
    {
        $this->base->setFacl('www/var', 'www-data', 'rwX', true, true);
        $this->assertEquals('setfacl -R -d -m u:"www-data":rwX www/var', $this->base->getStacked());
    }

    // TODO: test setFacl() allowFail option

    public function testServiceReload()
    {
        $this->base->service('apache2');
        $this->assertEquals('service apache2 reload', $this->base->getStacked());
    }

    public function testServiceRestart()
    {
        $this->base->service('php-fpm', 'restart');
        $this->assertEquals('service php-fpm restart', $this->base->getStacked());
    }


    public function testSystemctlReload()
    {
        $this->base->systemctl('apache2');
        $this->assertEquals('systemctl reload apache2', $this->base->getStacked());
    }

    public function testSystemctlRestart()
    {
        $this->base->systemctl('php-fpm', 'restart');
        $this->assertEquals('systemctl restart php-fpm', $this->base->getStacked());
    }

    public function testToString()
    {
        $this->base->set('-e', '-v')
            ->set('-o pipefail')
            ->changeDir('/opt/approot')
            ->makeDir('build-new')
            ->delete('previous')
            ->renameIfExists('current', 'previous')
            ->link('build-new', 'current')
        ;

        $reference = file_get_contents('tests/test.sh');

        $this->assertEquals(
            $reference,
            (string)$this->base
        );

        $this->assertEquals(
            $reference,
            $this->base->generateScript()
        );

        $this->assertEquals(
            $reference,
            $this->base->prettyPrint()
        );
    }
}
