<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Framework\Container\Container;

require_once(__DIR__ . '/../www/vendor/autoload.php');

class ContainerTest {
    public function testSetValue() {
        $container = new Container();
        $container->setValue('hello', 'world');
        $container->setValue('price', 1000);

        assert('world' === $container->get('hello'));
        assert(1000 === $container->get('price'));
    }

    public function testSetClosure() {
        $container = new Container();

        $container->setClosure('test', function() {
            return 'hello';
        });

        assert('hello' === $container->get('test'));
    }

    /**
     * クロージャが一度しか呼ばれないことをテスト
     */
    public function testClosureCalledOnce() {
        $container = new Container();

        $container->setClosure('randomValue', function() {
            return rand();
        });

        $rand1 = $container->get('randomValue');
        $rand2 = $container->get('randomValue');
        assert($rand1 === $rand2);
    }

    public function testDependency() {
        $container = new Container();
        $container->setClosure('text1', function($container) {
            $text2 = $container->get('text2');
            return 'hello, ' . $text2;
        });
        $container->setClosure('text2', function() {
            return 'world';
        });

        $text = $container->get('text1');
        assert($text === 'hello, world');
    }
}

$test = new ContainerTest();
$test->testSetValue();
$test->testSetClosure();
$test->testClosureCalledOnce();
$test->testDependency();