<?php
namespace Czim\CmsUploadModule\Test;

abstract class WebTestCase extends TestCase
{

    /**
     * @return string
     */
    protected function getTestBootCheckerBinding()
    {
        return \Czim\CmsCore\Test\Helpers\Core\MockWebBootChecker::class;
    }

}
