#!/usr/bin/perl
print "Cache-Control: no-cache\n";
print "Content-type: text/html \n\n";

# print HTML file top
print <<END;
<!DOCTYPE html>
<html><head><!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-P8836VK7');</script>
<!-- End Google Tag Manager --><title>GET Request Echo</title>
</head><body><!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P8836VK7"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) --><h1 align="center">Get Request Echo</h1>
<hr>
END

# The Query String is simply an environment variable
print "<b>Query String:</b> $ENV{QUERY_STRING}<br />\n";

# Credit for this code to parse the Query string:
# https://www.mediacollege.com/internet/perl/query-string.html
if (length ($ENV{'QUERY_STRING'}) > 0){
  $buffer = $ENV{'QUERY_STRING'};
  @pairs = split(/&/, $buffer);
  foreach $pair (@pairs) {
    ($name, $value) = split(/=/, $pair);
    $value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
    $in{$name} = $value; 
  }
}

#Print out the Query String
$loop = 0;
foreach my $key (%in) {
  $loop += 1;
  if($loop % 2 != 0) {
    print "$key = $in{$key}<br/>\n";
  }
}

# Print the HTML file bottom
print "</body></html>";

