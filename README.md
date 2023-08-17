# Odie Plugin Demo

Odie Plugin demo. 

### Installation From Git Repo

The repo should be production-ready enough to download the zip directly from github UI or simply clone to the plugins directory of your choice. 

### Development

The site needs to be talking to your wpcom sandbox, via JETPACK__SANDBOX_DOMAIN which you can define in wp-config.php or use the [Companion plugin](https://github.com/Automattic/companion) to set. 

Follow these instructions to set up Odie locally: https://wp.me/PCYsg-Skx/#chatbot-widget

Build and sync the odie-client/ widget to wpcom. In the ai-services/odie-client, run `yarn widget:build:wpcom && yarn widget:sync:wpcom`

Point widgets.wp.com to your sandbox in hosts file. 

### Testing

Once the site is connected as your a11n wpcom user, you should see a button to chat.

## Security

Need to report a security vulnerability? Go to [https://automattic.com/security/](https://automattic.com/security/) or directly to our security bug bounty site [https://hackerone.com/automattic](https://hackerone.com/automattic).

## License

Odie Plugin Demo is licensed under [GNU General Public License v2 (or later)](./LICENSE.txt)

## Meta 

Ignore this for now. It's for auto-updates, which isn't working yet. 
`~Current Version:0.3.2-alpha~`

