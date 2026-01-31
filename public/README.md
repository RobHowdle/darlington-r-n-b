To run the site

- Clone the into xamp htdocs
- Make sure you have Node installed and the xamp server is running
- Have the extension Live Sass Compiler running
- In the Live Sass Compiler Settings, find the settings.json file and make sure this is in it
	```
    "liveSassCompile.settings.formats": [
		{
			"format": "expanded",
			"extensionName": ".css",
			"savePath": "./css",
			"savePathReplacementPairs": null
		}
	]
    ```
- Start Watching Sass, make a change in a sass file and verify it works, then undo that change.