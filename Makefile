package:
#@pear channel-discover sinkcup.github.io/pear; onion build --pear;
	@php pyrus.phar channel-discover sinkcup.github.io/pear; onion build --pear;

install:
#@pear channel-discover sinkcup.github.io/pear; pear install sinkcup/Services_Apple_PushNotification;
	@php pyrus.phar channel-discover sinkcup.github.io/pear; php pyrus.phar install sinkcup/Services_Apple_PushNotification;

test:
	phpunit ./tests/

