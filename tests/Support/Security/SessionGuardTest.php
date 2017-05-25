<?php
namespace Czim\CmsUploadModule\Test\Support\Security;

use Czim\CmsUploadModule\Support\Security\SessionGuard;
use Czim\CmsUploadModule\Test\TestCase;

class SessionGuardTest extends TestCase
{

    /**
     * @test
     */
    function it_links_an_id_to_the_current_session()
    {
        $guard = new SessionGuard;

        static::assertFalse($guard->check(44), 'Check should fail before link is made');

        static::assertSame($guard, $guard->link(44));

        static::assertTrue($guard->check(44), 'Check should succeed after link is made');
    }

    /**
     * @test
     * @depends it_links_an_id_to_the_current_session
     */
    function it_unlinks_an_id_from_the_current_session()
    {
        $guard = new SessionGuard;
        $guard->link(44);

        static::assertSame($guard, $guard->unlink(44));

        static::assertFalse($guard->check(44), 'Check should fail after link is broken');
    }

    /**
     * @test
     */
    function it_silently_ignores_an_already_unlinked_id_on_unlink()
    {
        $guard = new SessionGuard;

        static::assertSame($guard, $guard->unlink(44));
    }

    /**
     * @test
     */
    function it_returns_whether_it_is_enabled()
    {
        $guard = new SessionGuard;

        $this->app['config']->set('cms-upload-module.restrict.session', true);

        static::assertTrue($guard->enabled());

        $this->app['config']->set('cms-upload-module.restrict.session', false);

        static::assertFalse($guard->enabled());
    }

}
