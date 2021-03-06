LatentSee - HTTP Performance Visualizer
=======================================

latentsee.php downloads a series of files from the webserver and plots response times.

Web users outside of the US often wait too long for pages to load because site owners have chosen cheaper offshore hosting options. I wrote latentsee to investigate the impact of this and was surprised by the results. I decided to make latentsee freely available in the interests of speeding up the web.

## Installation

### Running the Docker image

Docker provides a super simple way to run latentsee!
```
$ docker run -p 80:80 mbailey/latentsee
```

### Installing the old fashioned way

Simply disable compression and drop latentsee.php onto your Apache webserver

The following line can disable gzip on a per directory basis.

    BrowserMatch ^. no-gzip

You can add it to your vhost config or put it into a .htaccess file
