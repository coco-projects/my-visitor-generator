<?php

    use Coco\htmlBuilder\dom\Document;
    use Coco\htmlBuilder\dom\DomBlock;
    use Coco\htmlBuilder\dom\DoubleTag;

    use Coco\htmlBuilder\attrs\AttrRegistry;
    use Coco\htmlBuilder\attrs\ClassAttr;
    use Coco\htmlBuilder\attrs\DataAttr;
    use Coco\htmlBuilder\attrs\RawAttr;
    use Coco\htmlBuilder\attrs\StandardAttr;
    use Coco\htmlBuilder\attrs\StyleAttr;
    use Coco\htmlBuilder\dom\SingleTag;

    require '../vendor/autoload.php';

    DomBlock::$var['name'] = '哈哈哈哈哈';

    $dom1 = Document::ins()->inner(function(\Coco\htmlBuilder\dom\Document $this_, array &$inner) {
        $this_->appendSubsection('TITLE', DomBlock::$var['name']);

        $d = DoubleTag::ins('div')->inner(function(\Coco\htmlBuilder\dom\DoubleTag $this_, array &$inner) {

            $this_->appendRootSection('CSS_LIB', [
                SingleTag::ins('link')->inner(function(SingleTag $this_, array &$inner) {
                    $this_->getAttr('href')->setAttrKv('href', 'https://baidu.com/a.css');
                    $this_->getAttr('rel')->setAttrKv('rel', 'stylesheet');
                    $this_->getAttr('crossorigin')->setAttrKv('crossorigin', 'anonymous');
                }),
            ]);

            $this_->getAttr('class')->addAttrsArray([
                "layer",
                "layer-text",
            ]);

            $d1 = $this_->getCopy();

            $d1->getAttr('class')->addAttrsArray([
                "aaa",
                "bbb",
            ])->removeAttr('aaa');


            $s       = SingleTag::ins('hr');
            $inner[] = [
                'hello1',
                $s,
                $d1,
                function() {
                    return 'hello6';
                },
                [
                    'hello4',
                    $s,
                    'hello5',
                ],
                $s,
                '-----------------',
            ];

            $d1->getAttr('class')->addAttrsArray([
                "aaa",
                "bbb",
            ])->removeAttr('bbb');

            $inner[] = [
                $d1,
            ];

        });

        $inner[] = [
            'hello1',
            $d,
            function() {
                return 'hello6';
            },
            [
                'hello4',
                'hello5',
            ],
        ];
    });

    print_r($dom1->render());
    //    print_r($dom1->toArrayAll());
    //        print_r($dom1->toJson());
    //        print_r($dom1->toRaw());

