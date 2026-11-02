<?php

use Inertia\Testing\AssertableInertia;

test('the survey starts on the language screen', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Feedback/Welcome'));
});

test('choosing a language still opens the first survey step', function () {
    $this->post(route('feedback.language'), ['language' => 'id'])
        ->assertRedirect(route('feedback.step', ['step' => 2]));

    $this->get(route('feedback.step', ['step' => 2]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Feedback/Page2')
            ->has('countries')
            ->has('feedback'));
});

test('error pages share the branded layout and keep their actions', function (int $code, string $heading) {
    $html = view("errors.{$code}")->render();

    expect($html)
        ->toContain('/images/bali-in-bali-logo.png')
        ->toContain((string) $code)
        ->toContain($heading)
        ->toContain(route('home'))
        ->toContain('javascript:location.reload()');
})->with([
    [403, 'Access Denied'],
    [404, 'Page Not Found'],
    [419, 'Session End'],
    [429, 'Too Many Request'],
    [500, 'Internal Server Error'],
    [503, 'Maintenance'],
]);

test('the rate limit and maintenance pages keep their automatic reload', function () {
    expect(view('errors.429')->render())
        ->toContain('id="timer"')
        ->toContain('window.location.reload()');

    expect(view('errors.503')->render())
        ->toContain('setTimeout(() => window.location.reload(), 30000)');
});

test('the brand assets the interface points at exist', function () {
    expect(public_path('images/bali-in-bali-logo.png'))->toBeFile()
        ->and(resource_path('images/bali-in-bali-flower.png'))->toBeFile();
});

test('the flower mask goes through vite so it also loads on the dev server', function () {
    expect(file_get_contents(resource_path('css/survey.css')))
        ->toContain("url('../images/bali-in-bali-flower.png')")
        ->not->toContain("url('/images/");
});
