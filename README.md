# Twillio SMS Reader for Unsubscribes / Resubscribes

## What is this?

This is a plugin that allows users to be unsubscribed/resubscribed from SMS's by replying with one of several given key words. eg `STOP`. With this plugin enabled, Mautic will automatically put users who reply in the `Do not contact` list and also give them a tag `SMS Unsubscribed`.

It requires Twillio, and you will need your Twillio account details. These look like `AB12f54b8e4a07f484487bXXXXXXXXXXXX`

## Installation

Clone this repo to `/plugins/SmsreaderBundle` in your Mautic install.
The folder _must_ be named `SmsreaderBundle`

After copying, clear the Mautic cache

```
cd /directory/to/mautic
rm -r app/cache/*
```

Open up Mautic, and check that the plugin appears in the _Plugins_ section.

## Configuration

You need to fill in everything under the `Twillio SMS Reader` menu item in the Settings Sidebar.
It should be fairly self explanatory.

### Configuring Twillio

Twillio will have to make a request to the `/sms/callback` URI on your web server.

eg. `http://mautic.kfc.com/sms/callback`


## Testing

You can easily test to see if the gateway is working by doing a Curl request similar to the following:

```
curl -X POST -H "Cache-Control: no-cache" -d 'AccountSid=YOURACCOUNTSID&SmsMessageSid=SM27e5c&Body=YOURBODY&From=%2B61411111111' "http://localhost/sms/callback"
```

Replace `YOURACCOUNTSID` with your Account ID, `61411111111` with a mobile number from a lead in Mautic, `YOURBODY` with one of the Keywords you configured and `localhost` with the address of your Mautic server.

If all goes well, you'll see a JSON response like:

```
{"message": "Unsubscribe successfully processed"}
```

## License

GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
