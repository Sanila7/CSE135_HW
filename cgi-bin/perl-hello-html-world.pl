#!/usr/bin/perl
use strict;
use warnings;
use POSIX qw(strftime);

my $time = strftime("%Y-%m-%d %H:%M:%S", localtime);
my $ip   = $ENV{'REMOTE_ADDR'} || 'Unknown';

print "Content-type: text/html\n\n";

print <<HTML;
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hello from Perl</title>
</head>
<body>
    <h1>Hello from Perl!</h1>

    <p><strong>Team Member:</strong> Sanila Silva (Solo)</p>
    <p><strong>Language:</strong> Perl</p>
    <p><strong>Generated at:</strong> $time</p>
    <p><strong>Your IP address:</strong> $ip</p>

    <p><a href="/">Back to Home</a></p>
</body>
</html>
HTML
