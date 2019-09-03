# miniprinter
Use Miniprinter with GPIO Orange PI

To start on reboot

Run the following commands:

sudo cp -i ~/Desktop/test_code.py /bin

sudo crontab -e

Add the following line and save it:

@reboot python /bin/test_code.py &

EXAMPLE:

@reboot cd /home/print/Notebooks/escpos-php/ && sudo python3 print.py &
