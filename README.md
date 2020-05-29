# wp-theme-color-picker

wp-theme-color-picker is a wordpress plugin designed to easily manipulate theme colors for your wordpress site.

## How it works

The plugin relies on an AWS micro-service to get the options and create CSS from chosen options. The micro-service will consume the options sent to its API,
use SASS to render a new CSS file, and return the CSS as a string. The plugin will then update the styles.custom.css file in the plugin.

## Contributors

- Josh Zearfoss
- Zach Heindel
