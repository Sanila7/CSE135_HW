#!/usr/bin/perl
use strict;
use warnings;
use JSON;

print "Content-Type: application/json\r\n";
print "Cache-Control: no-cache\r\n";
print "\r\n";

my $date    = scalar localtime();
my $address = $ENV{REMOTE_ADDR} // "";

my %message = (
  title   => "Hello, Sanila!",
  heading => "Hello, Sanila!",
  message => "This page was generated with Perl using JSON.pm",
  date    => $date,
  ip      => $address,
);

print encode_json(\%message);
