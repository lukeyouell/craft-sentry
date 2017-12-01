<img src="resources/img/icon.png" alt="icon" width="100" height="100">

# Sentry plugin for Craft CMS 3.x

Error tracking that helps developers monitor and fix crashes in real time. Iterate continuously. Boost efficiency. Improve user experience.

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require lukeyouell/craft3-sentry

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Sentry.

## Configuring Sentry

Upon successful installation you will be required to supply an authentication token with `project:read` enabled.

[Authentication tokens are available here](https://sentry.io/api/).

A [Sentry](https://sentry.io) account is required, if you don't already have one [click here to create one](https://sentry.io/signup).

Free & paid plans are available.

## Using Sentry

After entering your Sentry authentication token, you will be required to select a Project along with a corresponding Client DSN.

## Sentry Roadmap

Some things to do, and ideas for potential features:

- Dev mode toggle
- Create project from within the CP

Brought to you by [Luke Youell](https://github.com/lukeyouell)
