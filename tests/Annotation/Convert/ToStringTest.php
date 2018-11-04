<?php

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace KevinGH\Box\Annotation\Convert;

use KevinGH\Box\Annotation\TestTokens;
use KevinGH\Box\Annotation\Tokens;

/**
 * @covers \KevinGH\Box\Annotation\Convert\ToString
 */
class ToStringTest extends TestTokens
{
    /**
     * @var ToString
     */
    private $converter;

    public function getStrings(): array
    {
        $strings = [];
        $tokens = $this->getTokens();

        /*
         * @Annotation
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation
FORMATTED
        ];

        /*
         * @Annotation()
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation()
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation()
FORMATTED
        ];

        /*
         * @Annotation ()
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation()
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation()
FORMATTED
        ];

        /*
         * @A
         * @B
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@A
@B
UNFORMATTED
            ,
            <<<'FORMATTED'
@A
@B
FORMATTED
        ];

        /*
         * @A()
         * @B()
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@A()
@B()
UNFORMATTED
            ,
            <<<'FORMATTED'
@A()
@B()
FORMATTED
        ];

        /*
         * @Namespaced\Annotation
         */
        $strings[] = [
            array_shift($tokens),
            <<<UNFORMATTED
@Namespaced\Annotation
UNFORMATTED
            ,
            <<<FORMATTED
@Namespaced\Annotation
FORMATTED
        ];

        /*
         * @Namespaced\ Annotation
         */
        $strings[] = [
            array_shift($tokens),
            <<<UNFORMATTED
@Namespaced\Annotation
UNFORMATTED
            ,
            <<<FORMATTED
@Namespaced\Annotation
FORMATTED
        ];

        /*
         * @Namespaced\Annotation()
         */
        $strings[] = [
            array_shift($tokens),
            <<<UNFORMATTED
@Namespaced\Annotation()
UNFORMATTED
            ,
            <<<FORMATTED
@Namespaced\Annotation()
FORMATTED
        ];

        /*
         * @Annotation("string")
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation("string")
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    "string"
)
FORMATTED
        ];

        /*
         * @Annotation(
         *     "string"
         * )
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation("string")
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    "string"
)
FORMATTED
        ];

        /*
         * @Annotation(123, "string", 1.23, CONSTANT, false, true, null)
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(123,"string",1.23,CONSTANT,false,true,null)
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    123,
    "string",
    1.23,
    CONSTANT,
    false,
    true,
    null
)
FORMATTED
        ];

        /*
         * @Annotation(constant, FALSE, TRUE, NULL)
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(constant,FALSE,TRUE,NULL)
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    constant,
    FALSE,
    TRUE,
    NULL
)
FORMATTED
        ];

        /*
         * @Annotation(key="value")
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(key="value")
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    key="value"
)
FORMATTED
        ];

        /*
         * @Annotation(a="b", c="d")
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(a="b",c="d")
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    a="b",
    c="d"
)
FORMATTED
        ];

        /*
         * @Annotation(
         *     a=123,
         *     b="string",
         *     c=1.23,
         *     d=CONSTANT,
         *     e=false,
         *     f=true,
         *     g=null
         * )
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(a=123,b="string",c=1.23,d=CONSTANT,e=false,f=true,g=null)
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    a=123,
    b="string",
    c=1.23,
    d=CONSTANT,
    e=false,
    f=true,
    g=null
)
FORMATTED
        ];

        /*
         * @Annotation({})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {}
)
FORMATTED
        ];

        /*
         * @Annotation(key={})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(key={})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    key={}
)
FORMATTED
        ];

        /*
         * @Annotation({"string"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({"string"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        "string"
    }
)
FORMATTED
        ];

        /*
         * @Annotation(
         *     {
         *         "string"
         *     }
         * )
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({"string"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        "string"
    }
)
FORMATTED
        ];

        /*
         * @Annotation({123, "string", 1.23, CONSTANT, false, true, null})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({123,"string",1.23,CONSTANT,false,true,null})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        123,
        "string",
        1.23,
        CONSTANT,
        false,
        true,
        null
    }
)
FORMATTED
        ];

        /*
         * @Annotation({key="value"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({key="value"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        key="value"
    }
)
FORMATTED
        ];

        /*
         * @Annotation({"key"="value"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({"key"="value"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        "key"="value"
    }
)
FORMATTED
        ];

        /*
         * @Annotation({a="b", c="d"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({a="b",c="d"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        a="b",
        c="d"
    }
)
FORMATTED
        ];

        /*
         * @Annotation({a="b", "c"="d", 123="e"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({a="b","c"="d",123="e"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        a="b",
        "c"="d",
        123="e"
    }
)
FORMATTED
        ];

        /*
         * @Annotation({key={}})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({key={}})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        key={}
    }
)
FORMATTED
        ];

        /*
         * @Annotation(a={b={}})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(a={b={}})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    a={
        b={}
    }
)
FORMATTED
        ];

        /*
         * @Annotation({key: {}})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({key:{}})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        key: {}
    }
)
FORMATTED
        ];

        /*
         * @Annotation(a={b: {}})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(a={b:{}})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    a={
        b: {}
    }
)
FORMATTED
        ];

        /*
         * @Annotation({key: "value"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({key:"value"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        key: "value"
    }
)
FORMATTED
        ];

        /*
         * @Annotation({"key": "value"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({"key":"value"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        "key": "value"
    }
)
FORMATTED
        ];

        /*
         * @Annotation({a: "b", c: "d"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({a:"b",c:"d"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        a: "b",
        c: "d"
    }
)
FORMATTED
        ];

        /*
         * @Annotation({a: "b", "c": "d", 123: "e"})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({a:"b","c":"d",123:"e"})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        a: "b",
        "c": "d",
        123: "e"
    }
)
FORMATTED
        ];

        /*
         * @Annotation(
         *     {
         *         "a",
         *         {
         *             {
         *                 "c"
         *             },
         *             "b"
         *         }
         *     }
         * )
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({"a",{{"c"},"b"}})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        "a",
        {
            {
                "c"
            },
            "b"
        }
    }
)
FORMATTED
        ];

        /*
         * @Annotation(@Nested)
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(@Nested)
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    @Nested
)
FORMATTED
        ];

        /*
         * @Annotation(@Nested())
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(@Nested())
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    @Nested()
)
FORMATTED
        ];

        /*
         * @Annotation(@Nested, @Nested)
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(@Nested,@Nested)
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    @Nested,
    @Nested
)
FORMATTED
        ];

        /*
         * @Annotation(@Nested(), @Nested())
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(@Nested(),@Nested())
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    @Nested(),
    @Nested()
)
FORMATTED
        ];

        /*
         * @Annotation(
         *     @Nested(),
         *     @Nested()
         * )
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(@Nested(),@Nested())
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    @Nested(),
    @Nested()
)
FORMATTED
        ];

        /*
         * @Annotation(key=@Nested)
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(key=@Nested)
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    key=@Nested
)
FORMATTED
        ];

        /*
         * @Annotation(a=@Nested(),b=@Nested)
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(a=@Nested(),b=@Nested)
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    a=@Nested(),
    b=@Nested
)
FORMATTED
        ];

        /*
         * @Annotation({key=@Nested})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({key=@Nested})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        key=@Nested
    }
)
FORMATTED
        ];

        /*
         * @Annotation({a=@Nested(),b=@Nested})
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation({a=@Nested(),b=@Nested})
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    {
        a=@Nested(),
        b=@Nested
    }
)
FORMATTED
        ];

        /*
         * @Annotation(
         *     @Nested(
         *         {
         *             "a",
         *             {
         *                 {
         *                     "c"
         *                 },
         *                 "b"
         *             }
         *         }
         *     ),
         *     @Nested(
         *         {
         *             "d",
         *             {
         *                 {
         *                     "f",
         *                 },
         *                 "e"
         *             }
         *         }
         *     )
         * )
         */
        $strings[] = [
            array_shift($tokens),
            <<<'UNFORMATTED'
@Annotation(@Nested({"a",{{"c"},"b"}}),@Nested({"d",{{"f",},"e"}}))
UNFORMATTED
            ,
            <<<'FORMATTED'
@Annotation(
    @Nested(
        {
            "a",
            {
                {
                    "c"
                },
                "b"
            }
        }
    ),
    @Nested(
        {
            "d",
            {
                {
                    "f",
                },
                "e"
            }
        }
    )
)
FORMATTED
        ];

        return $strings;
    }

    /**
     * @dataProvider getStrings
     *
     * @param mixed $tokens
     * @param mixed $unformatted
     * @param mixed $formatted
     */
    public function testConvert($tokens, $unformatted, $formatted): void
    {
        $tokens = new Tokens($tokens);

        $this->converter->setIndentSize(0);
        $this->converter->useColonSpace(false);

        $this->assertEquals(
            $unformatted,
            $this->converter->convert($tokens)
        );

        $this->converter->setIndentSize(4);
        $this->converter->useColonSpace(true);

        $this->assertEquals(
            $formatted,
            $this->converter->convert($tokens)
        );
    }

    public function testSetBreakChar(): void
    {
        $this->assertSame(
            $this->converter,
            $this->converter->setBreakChar("\r")
        );

        $this->assertEquals(
            "\r",
            $this->getPropertyValue($this->converter, 'break')
        );
    }

    public function testSetIndentChar(): void
    {
        $this->assertSame(
            $this->converter,
            $this->converter->setIndentChar("\t")
        );

        $this->assertEquals(
            "\t",
            $this->getPropertyValue($this->converter, 'char')
        );
    }

    public function testSetIndentSize(): void
    {
        $this->assertSame(
            $this->converter,
            $this->converter->setIndentSize(123)
        );

        $this->assertEquals(
            123,
            $this->getPropertyValue($this->converter, 'size')
        );
    }

    public function testUseColonSpace(): void
    {
        $this->assertSame(
            $this->converter,
            $this->converter->useColonSpace(true)
        );

        $this->assertTrue($this->getPropertyValue($this->converter, 'space'));
    }

    protected function setUp(): void
    {
        $this->converter = new ToString();
    }
}
