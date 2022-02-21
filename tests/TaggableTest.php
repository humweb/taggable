<?php

namespace Humweb\Taggable\Tests;

use Humweb\Taggable\Tests\Models\Post;

beforeEach(function () {
    $this->post1 = Post::create(['title' => 'Test Post1']);
    $this->post2 = Post::create(['title' => 'Test Post2']);
});

it('can tag post', function () {
    $this->post1->attachTag('Test 1');
    $this->post1->attachTag('Test 2');
    expect($this->post1->tags[0]->name)->toEqual('Test 1');
    expect($this->post1->tags[1]->name)->toEqual('Test 2');
});

it('can add tags to post', function () {
    $this->post1->attachTags(['Test 1', 'Test 2']);
    expect($this->post1->tags[0]->name)->toEqual('Test 1');
    expect($this->post1->tags[1]->name)->toEqual('Test 2');
});

it('can get post by tag', function () {
    $this->post1->attachTags(['Test 1', 'Test 2']);
    expect(Post::withAnyTags('Test 1')->first()->title)->toEqual('Test Post1');
    expect(Post::withAnyTags('Test 2')->first()->title)->toEqual('Test Post1');
});

it('can detach tags', function () {
    $this->post1->attachTags(['Test 1', 'Test 2']);
    expect($this->post1->tags()->first()->name)->toEqual('Test 1');

    $this->post1->detachTags('Test 1');
    expect($this->post1->tags()->first()->name)->toEqual('Test 2');
});

it('can sync tags', function () {
    $this->post1->attachTags(['Test 1', 'Test 2']);
    expect($this->post1->tags()->first()->name)->toEqual('Test 1');

    $this->post1->syncTags(['Test 3', 'Test 4']);
    $tags = $this->post1->tags()->get();
    expect($tags[0]->name)->toEqual('Test 3');
    expect($tags[1]->name)->toEqual('Test 4');
    expect(isset($tags[2]))->toBeFalse();
});

it('can create tag slug', function () {
    $this->post1->attachTags(['Test 1', 'Test 2']);
    expect($this->post1->tags()->first()->slug)->toEqual('test-1');
});
