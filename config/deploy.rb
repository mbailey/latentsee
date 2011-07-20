role :web, 'linode.latentsee.com',
           'ultraserve.latentsee.com',
           'uat.int.failmode.com'
#           'rails@mikebailey-001.vm.brightbox.net'

task :deploy, :roles => :web do
  upload 'latentsee.php', '/var/www/latentsee/latentsee.php'
end

