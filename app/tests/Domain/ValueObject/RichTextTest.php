<?php
declare(strict_types=1);

namespace Tests\App\Domain\ValueObject;

use App\Domain\ValueObject\RichText;

class RichTextTest extends \Tests\App\BaseTestCase
{
    public function testUnsafeTagsAreStripped(): void
    {
        $unsafe = '<script>alert("naughty");</script>';

        $richText = new RichText($unsafe);

        $this->assertSame('alert("naughty");', $richText->getContent());
    }

    public function testGetForDisplay(): void
    {
        $input = <<<INPUT
<p>
Line 1
</p>
<p>
Line 2
</p>
<p></p>
<p>
Line 3
</p>
<p></p>
<p></p>
<p>Line 4</p>
INPUT;

        $expectedOutput = '<p>Line 1<br>Line 2</p><p>Line 3</p><p>Line 4</p>';

        $richText = new RichText($input);
        $this->assertSame($expectedOutput, $richText->getContentForDisplay());
    }
}
