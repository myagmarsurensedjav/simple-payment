<?php

use MyagmarsurenSedjav\SimplePayment\Tests\TestCase;
use Pest\Mock\Mock;

uses(TestCase::class)->in(__DIR__);

/**
 * Creates a new mock with the given class or object.
 *
 * @template TObject as object
 *
 * @param  class-string<TObject>|TObject  $object
 * @return Mock<TObject>
 */
function mockWithPest(object|string $object): Mock
{
    return new Mock($object);
}
