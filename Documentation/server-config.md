# Configuration for Webservers

## nginx

```nginx
location ~* \.(?:jpg|jpeg|gif|png|ico|cur|gz|svg|svgz|mp4|ogg|ogv|webm|htc)$ {
    # your configuration here
    try_files $uri /index.php?$uri;
}
```

## Apache2

```apacheconf
RewriteCond %{DOCUMENT_ROOT}/%{REQUEST_FILENAME} !-f
RewriteRule ^/(.*)$ /index.php?%{REQUEST_URI} [P,QSA,L]
```
