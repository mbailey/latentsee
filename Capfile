# load 'deploy' if respond_to?(:namespace) # cap2 differentiator

# load 'config/deploy' # remove this line to skip loading any of the default tasks

role :web, 'slicehost.latentsee.com', 
           'ultraserve.latentsee.com'

task :deploy, :roles => :web do
  upload 'latentsee.php', '/var/www/latentsee/latentsee.php'
end

