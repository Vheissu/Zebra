<?php

namespace App\Actions;

use App\Models\Story;
use App\Models\User;
use App\Support\Url;
use Illuminate\Support\Facades\Validator;

class SubmitStory
{
    /**
     * Validation rules shared by the submit form, the edit form and the API.
     *
     * @return array<string, mixed>
     */
    public static function rules(?Story $story = null): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'url' => ['nullable', 'string', 'max:2048', 'url:http,https', 'required_without:text'],
            'text' => ['nullable', 'string', 'max:10000', 'required_without:url'],
        ];
    }

    public static function messages(): array
    {
        return [
            'url.required_without' => 'Give it a link, some text, or both.',
            'text.required_without' => 'Give it a link, some text, or both.',
        ];
    }

    /**
     * An existing story for the same link, if it was posted recently enough to count as a duplicate.
     */
    public function duplicateOf(?string $url): ?Story
    {
        if (! $url) {
            return null;
        }

        return Story::where('url_hash', Url::hash($url))
            ->where('created_at', '>=', now()->subDays(config('zebra.duplicate_days')))
            ->latest()
            ->first();
    }

    /**
     * @param  array{title: string, url?: ?string, text?: ?string}  $input
     */
    public function handle(User $user, array $input): Story
    {
        $data = Validator::make($input, static::rules(), static::messages())->validate();

        $story = new Story([
            'title' => trim($data['title']),
            'url' => $data['url'] ?? null,
            'text' => isset($data['text']) ? trim($data['text']) : null,
        ]);

        $story->user()->associate($user)->save();

        return $story;
    }
}
