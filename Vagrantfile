Vagrant.configure("2") do |config|
    config.vm.box = "ubuntu/bionic64"
    config.vm.network "forwarded_port", guest: 5900, host: 5900

    config.vm.provision "shell", inline: <<-SHELL
      sudo apt-get update
      sudo apt-get install -y tightvncserver
       echo "mypassword" | vncpasswd -f > ~/.vnc/passwd
      chmod 600 ~/.vnc/passwd
       vncserver :1 -geometry 1280x720 -depth 24
    SHELL
  end

