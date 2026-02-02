#!/usr/bin/perl
use strict;
use warnings;

print "Cache-Control: no-cache\r\n";
print "Content-Type: text/html\r\n\r\n";

my $date    = scalar localtime();
my $address = $ENV{'REMOTE_ADDR'} // '';

print <<"HTML";
<!DOCTYPE html>
<html>
<head>
  <!-- Google Tag Manager -->
  <script>
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-P8836VK7');
  </script>
  <!-- End Google Tag Manager -->

  <title>Hello Sanila Silva</title>
</head>
<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P8836VK7"
      height="0" width="0" style="display:none;visibility:hidden"></iframe>
  </noscript>
  <!-- End Google Tag Manager (noscript) -->

  <h1 align="center">Hello Sanila Silva</h1>
  <hr />

  <p>Hello Sanila Silva</p>
  <p>This page was generated with the Perl programming language</p>
  <p>This program was generated at: $date</p>
  <p>Your current IP Address is: $address</p>
</body>
</html>
HTML
