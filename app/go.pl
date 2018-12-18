#!/usr/bin/perl


$f = "config.xml";
$fnew = $f.".bak";


open FOO, $f or die "cannot open $f\n";

while (<FOO>) {
	if (m/ACS2.(\d+)/) {
		$s = $_;
		$version = $1;
		$version++;
		$s =~ s/ACS2.(\d+)/ACS2.$version/;
		printf($s);
	}
	else {
		printf $_;
	}
}
close FOO;
