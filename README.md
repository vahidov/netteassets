In first run search rev-manifest.json in the root of project and cache results to Nette-configurator file.

**Base use**

`<script type="text/javascript" src="{asset "/js/default.js"}"></script>`

In production mode that makes transformation and you see in resulted html `<script type="text/javascript" src="//www.youserver.com/js/default-693eb2d376.js"></script>`
 
In development mode used standart file name `<script type="text/javascript" src="https://localhost:9090/js/default.js"></script>`

**Livereload**

In latte template `{livereloadscript}`

In production mode return empty string `""`

In development mode return `<script type="text/javascript" src="//localhost:35729/livereload.js"></script>`
 
**config.neon**

```neon
assets:
		manifestFile: rev-manifest.json
		scripthost: "https://localhost"
		scriptport: "9090"
		livereloadhost: "https://localhost"
		livereloadport: 35729
```
Scripthost, scriptport, livereloadhost, livereloadport may be empty. In this case used `$_SERVER['HTTP_HOST']` for hosts and `80` for port values.