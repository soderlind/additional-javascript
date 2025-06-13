# WordPress Plugin GitHub Updater

A reusable WordPress plugin updater class that handles automatic updates from GitHub repositories using the `yahnis-elsts/plugin-update-checker` library.

## Table of Contents

- [Files Overview](#files-overview)
- [Quick Start](#quick-start)
- [Configuration Parameters](#configuration-parameters)
- [GitHub Workflow for Automatic Release Assets](#github-workflow-for-automatic-release-assets)
- [Usage Examples](#usage-examples)
- [Customizing the Class](#customizing-the-class)
- [Dependencies](#dependencies)
- [Key Features](#key-features)
- [Best Practices](#best-practices)
- [Error Handling](#error-handling)
- [License](#license)

## Files Overview

- `class-github-plugin-updater.php` - **Recommended generic class** with better error handling
- `class-additional-javascript-updater.php` - Modified original class with backward compatibility
- `example-generic-updater.php` - Example implementations

## Quick Start

### 1. Copy the updater class to your plugin
Copy `class-github-plugin-updater.php` to your plugin directory.

### 2. Include the class in your main plugin file
```php
require_once plugin_dir_path( __FILE__ ) . 'class-github-plugin-updater.php';
```

### 3. Initialize with your plugin's configuration

#### Simple Usage (Recommended)
```php
<?php
$updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create(
    'https://github.com/username/plugin-name',
    __FILE__,
    'plugin-name'
);
```

#### With Custom Branch
```php
<?php
$updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create(
    'https://github.com/username/plugin-name',
    __FILE__,
    'plugin-name',
    'develop'  // Branch name
);
```

#### With Release Assets
```php
<?php
$updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create_with_assets(
    'https://github.com/username/plugin-name',
    __FILE__,
    'plugin-name',
    '/plugin-name\.zip/'  // Regex pattern for zip file
);
```

#### Advanced Configuration
```php
<?php
$updater = new \Soderlind\WordPress\GitHub_Plugin_Updater( array(
    'github_url'            => 'https://github.com/username/plugin-name',
    'plugin_file'           => __FILE__,
    'plugin_slug'           => 'plugin-name',
    'branch'                => 'main',
    'name_regex'            => '/plugin-name-v[\d\.]+\.zip/',
    'enable_release_assets' => true,
) );
```

## Configuration Parameters

| Parameter | Required | Description | Example |
|-----------|----------|-------------|---------|
| `github_url` | Yes | GitHub repository URL | `'https://github.com/username/plugin-name'` |
| `plugin_file` | Yes | Path to main plugin file | `__FILE__` or `PLUGIN_CONSTANT_FILE` |
| `plugin_slug` | Yes | Plugin slug for WordPress | `'my-awesome-plugin'` |
| `branch` | No | Git branch to check for updates | `'master'` (default), `'main'`, `'develop'`.<br> [Caveat](https://github.com/YahnisElsts/plugin-update-checker?tab=readme-ov-file#how-to-release-an-update-1): If you set the branch to `master` , the update checker will look for recent releases and tags first. It'll only use the master branch if it doesn't find anything else suitable. |
| `name_regex` | No | Regex pattern for release assets | `'/plugin-name\.zip/'` |
| `enable_release_assets` | No | Whether to enable release assets | `true` if name_regex provided |

## GitHub Workflow for Automatic Release Assets

To automatically create zip files when you publish a GitHub release, include this workflow file in your repository. This is especially useful when using the `name_regex` parameter to filter release assets.

### Setup: Create `.github/workflows/on-release-add.zip.yml`

```yaml
name: On Release, Build release zip

on:
  release:
    types: [published]

jobs:
  build:
    name: Build release zip
    runs-on: ubuntu-latest
    permissions:
      contents: write
    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Build plugin # Remove or modify this step as needed
        run: |
          composer install --no-dev

      - name: Archive Release
        uses: thedoctor0/zip-release@b57d897cb5d60cb78b51a507f63fa184cfe35554 #0.7.6
        with:
          type: 'zip'
          filename: 'your-plugin-name.zip'  # Change this to match your plugin
          exclusions: '*.git* .editorconfig composer* *.md package.json package-lock.json'

      - name: Release
        uses: softprops/action-gh-release@c95fe1489396fe8a9eb87c0abf8aa5b2ef267fda #v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
        with:
          files: your-plugin-name.zip  # Change this to match your plugin
          tag_name: ${{ github.event.release.tag_name }}
```

### How it Works

1. **Trigger**: Automatically runs when you publish a GitHub release
2. **Build**: Installs production dependencies with `composer install --no-dev`
3. **Archive**: Creates a zip file excluding development files
4. **Attach**: Adds the zip file to the GitHub release as an asset

### Customizing the Workflow

- **Change filename**: Update both `filename:` and `files:` to match your plugin name
- **Modify exclusions**: Add or remove files/patterns from the `exclusions:` list
- **Build step**: Modify the build process (npm, webpack, etc.) as needed for your plugin

### Example Release Asset URLs

After the workflow runs, your release assets will be available at URLs like:

```
https://github.com/username/plugin-name/releases/latest/download/plugin-name.zip
```

**Real example from this plugin:**
```
https://github.com/soderlind/additional-javascript/releases/latest/download/additional-javascript.zip
```

### Using with the Updater

When using release assets, configure your updater like this:

```php
<?php
$updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create_with_assets(
    'https://github.com/username/plugin-name',
    __FILE__,
    'plugin-name',
    '/plugin-name\.zip/'  // This regex will match the zip created by the workflow
);
```

## Usage Examples

### Example 1: Basic Plugin Update
```php
<?php
// In your main plugin file
define( 'MY_PLUGIN_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'class-github-plugin-updater.php';

$my_plugin_updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create(
    'https://github.com/myusername/my-awesome-plugin',
    MY_PLUGIN_FILE,
    'my-awesome-plugin'
);
```

### Example 2: With Custom Branch and Asset Pattern
```php
<?php
$updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create_with_assets(
    'https://github.com/company/enterprise-plugin',
    __FILE__,
    'enterprise-plugin',
    '/enterprise-plugin-v[\d\.]+\.zip/',
    'release'  // Custom branch
);
```

### Example 3: Multiple Plugins in Same Project
```php
<?php
// Plugin A
$plugin_a_updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create_with_assets(
    'https://github.com/mycompany/plugin-suite',
    plugin_dir_path( __FILE__ ) . 'plugin-a/plugin-a.php',
    'plugin-a',
    '/plugin-a\.zip/'
);

// Plugin B
$plugin_b_updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create_with_assets(
    'https://github.com/mycompany/plugin-suite',
    plugin_dir_path( __FILE__ ) . 'plugin-b/plugin-b.php',
    'plugin-b',
    '/plugin-b\.zip/'
);
```

### Example 4: Full Configuration Control
```php
<?php
$updater = new \Soderlind\WordPress\GitHub_Plugin_Updater( array(
    'github_url'            => 'https://github.com/mycompany/premium-plugin',
    'plugin_file'           => __FILE__,
    'plugin_slug'           => 'premium-plugin',
    'branch'                => 'stable',
    'name_regex'            => '/premium-plugin-pro-v[\d\.]+\.zip/',
    'enable_release_assets' => true,
) );
```

## Customizing the Class

### Option 1: Rename the Class
If you want to avoid conflicts, rename the class:

```php
<?php
namespace YourCompany\YourPlugin;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class Your_Plugin_Updater extends \Soderlind\WordPress\GitHub_Plugin_Updater {
    // Inherit all functionality, customize as needed
}
```

### Option 2: Create Plugin-Specific Wrapper
Create a plugin-specific version:

```php
<?php
namespace YourCompany\YourPlugin;

class Your_Plugin_Updater {
    private $updater;
    
    public function __construct() {
        $this->updater = \Soderlind\WordPress\GitHub_Plugin_Updater::create(
            'https://github.com/yourcompany/your-plugin',
            YOUR_PLUGIN_FILE,
            'your-plugin'
        );
    }
}
```

## Dependencies

This updater requires the `yahnis-elsts/plugin-update-checker` library:

```bash
composer require yahnis-elsts/plugin-update-checker
```

Or download it manually from: https://github.com/YahnisElsts/plugin-update-checker

## Key Features

- ✅ **Automatic updates** from GitHub releases
- ✅ **Custom branch support** for development/staging
- ✅ **Release asset filtering** with regex patterns
- ✅ **Error handling** with debug logging
- ✅ **Multiple initialization methods** (static factories)
- ✅ **Parameter validation** with meaningful error messages
- ✅ **Flexible configuration** options
- ✅ **Fully documented** with examples

## Best Practices

1. **Use Constants**: Define your plugin file as a constant for consistency
2. **Static Factory Methods**: Use `::create()` methods for simpler initialization
3. **Error Handling**: The updater includes proper validation and debug logging
4. **Testing**: Test with different branch names and release asset patterns
5. **Documentation**: Document your specific configuration for future reference
6. **Namespace**: Consider using your own namespace to avoid conflicts

## Error Handling

The `GitHub_Plugin_Updater` class includes robust error handling:

- **Parameter Validation**: Required parameters are validated on construction
- **Exception Handling**: Gracefully handles errors during update check setup
- **Debug Logging**: Errors are logged when `WP_DEBUG` is enabled
- **Graceful Degradation**: Plugin continues to work even if updater fails


## License

GPL-2.0+ (same as the original plugin)
