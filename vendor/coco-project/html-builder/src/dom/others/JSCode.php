<?php

    declare(strict_types = 1);

    namespace Coco\htmlBuilder\dom\others;

    use Coco\htmlBuilder\dom\DomSection;
    use JShrink\Minifier;
    use MatthiasMullie\Minify\JS;

class JSCode extends DomSection
{
    public function __construct(string $code = '')
    {
        parent::__construct($code);
    }

    protected function afterRender(string &$sectionContents): void
    {
        if (!$this::$isDebug) {
            $sectionContents = Minifier::minify($sectionContents, ['flaggedComments' => true]);
        }
    }
}
