=== AI Text to Speech ===
Contributors: ElliotVS, RelyWP, freemius
Tags: ai, text to speech, ai tts, tts, text to audio
Donate link: https://www.paypal.com/donate/?hosted_button_id=RX28BBH7L5XDS
Requires at least: 4.7
Tested up to: 6.6.1
Stable tag: 2.0.3
License: GPLv3 or later.

Easily generate and display an audio version for your posts using OpenAI's Text to Speech API.

== Description ==

A simple plugin that allows you to create an AI generated audio version of your posts using OpenAI's Text to Speech API.

When the audio has been generated for a post, an audio player will be displayed automatically at the top of your post, which visitors can listen to.

This plugin is perfect for bloggers, content creators, and anyone who wants to provide an audio version of their posts for their visitors.

## Key Features

- Easily generate an audio version of your posts using OpenAI's Text to Speech API.
- Automatically display an audio player at the top of your post.
- Supports text to speech generation for over 50 languages.
- Optionally include the post title, author name and post date at the start of the audio.

## Pro Features

- Statistics tracking and analytics reporting.
- Integration with Dropbox to store generated audio files.
- Add custom text before and after the generated audio. This can be done on a global or per post basis.
- Fully replace the content used when generating the audio for a post.
- Audio player style customisation settings.

<a href="https://relywp.com/plugins/ai-text-to-speech/">Learn more about the PRO version.</a>

## Suggestions and Support

If you have any suggestions for additional functionality, need any help, or have found a bug, please create a <a href="https://wordpress.org/support/plugin/ai-text-to-speech/#new-topic-0">support ticket</a>.

== Installation ==
1. Upload the 'ai-text-to-speech' plugin to the '/wp-content/plugins/' directory.
2. Activate the affiliate plugin through the 'Plugins' menu in WordPress.
3. Modify additional plugin settings with your OpenAI key. View the "Setup Guide" link for more instructions.
4. Go to the post editor, and you will see a new meta box on the sidebar called "AI Text To Speech".

== Frequently Asked Questions ==

= How do I get an OpenAI key? =

You can get an OpenAI key by signing up for an account at [OpenAI.com](https://openai.com/).

= Does this plugin use a 3rd party service? =

Yes, this AI Text to Speech plugin relys on a 3rd party service "[OpenAI.com](https://openai.com/)" to generate the audio, so you will need to sign up for an account and get an API key to use this plugin. You can view the OpenAI terms of service [here](https://openai.com/policies/).

The plugin may also make calls to "[publish.twitter.com](https://publish.twitter.com/)" in order to retrieve the text/content from certain posts. You can view the Twitter terms of service [here](https://twitter.com/en/tos).

= Is the plugin free? =

This plugin is free to use, however it does have a paid "PRO" version which offers some additional features. 

The plugin does also depend on the OpenAI API in which their usage costs will apply.

= What languages are supported? =

OpenAI Text to Speech currently supports the following languages:

English, Afrikaans, Arabic, Armenian, Azerbaijani, Belarusian, Bosnian, Bulgarian, Catalan, Chinese, Croatian, Czech, Danish, Dutch, Estonian, Finnish, French, Galician, German, Greek, Hebrew, Hindi, Hungarian, Icelandic, Indonesian, Italian, Japanese, Kannada, Kazakh, Korean, Latvian, Lithuanian, Macedonian, Malay, Marathi, Maori, Nepali, Norwegian, Persian, Polish, Portuguese, Romanian, Russian, Serbian, Slovak, Slovenian, Spanish, Swahili, Swedish, Tagalog, Tamil, Thai, Turkish, Ukrainian, Urdu, Vietnamese, and Welsh.

You can generate spoken audio in these languages by providing the input text in the language of your choice.

= Do you provide support? =

Yes. If you need any help setting up the affiliate plugin, please free to <a href="https://wordpress.org/support/plugin/ai-text-to-speech/#new-topic-0">get in touch</a> and we'll be happy to help!

= How can I report issues/bugs with the plugin? =

You can report feature bugs by creating a <a href="https://wordpress.org/support/plugin/ai-text-to-speech/#new-topic-0">support ticket</a>. Please provide as much information as possible to make it easier for us to find a solution for you.

= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/ai-text-to-speech)

== Screenshots ==

1. Example of AI TTS generator on edit post page.
2. Example of AI TTS audio player on a blog post.

== Changelog ==

= Version 2.0.3 - 16th October 2024 =
- Fix: Fixed an error on the posts editor page in some cases showing: "Uncaught TypeError: in_array()"
- Fix: When converting tweets to audio, it now removes the picture link/info from the audio.
- Other: Updated to Freemius SDK 2.8.1

= Version 2.0.2 - 15th August 2024 =
- Fix: (PRO) Fixed an issue with the dropbox integration.

= Version 2.0.1 - 14th August 2024 =
- Tweak: Tweak to the spacing under the audio player.
- Tweak: (PRO) The post title now links to the edit post page, in the individual posts list on the statistics page.
- Fix: (PRO) Fixed an issue with the dropbox integration.

= Version 2.0.0 - 12th August 2024 =
- New: Added a PRO version of the plugin.
- New: (PRO) Added statistics tracking and analytics reporting.
- New: (PRO) Added integration with Dropbox to store generated audio files.
- New: (PRO) Added the ability to add custom text before and after the generated audio. This can be done on a global or per post basis.
- New: (PRO) Added the ability to fully replace the content used when generating the audio for a post.
- New: (PRO) Added some audio player style customisation settings.
- Improvement: Made some improvements to the content generation process, to include more pauses in areas where it makes sense.
- Improvement: Made the generation process more reliable for larger posts.

= Version 1.0.1 - 19th July 2024 =
- Tweak: Added some better error handling and error messages on the admin settings page if there is a connection issue with the OpenAI API.
- Tweak: Added an error log to the plugin settings page to help debug any issues.

= Version 1.0.0 - 19th January 2024 =
- Initial release