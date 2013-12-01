import os, sys, string

def compress(type, src_dir, dst_dir): # compress files in src_dir, replacing src_dir with dst_dir and inserting '.min' extension
    for src_path, directories, files in os.walk(src_dir):
        for src_file in files:
            if str(src_file).lower()[-len(type) - 1:] == '.' + type: # must obey file extension
                if src_path[len(src_path) - 1] != '/':
                    src_path += '/'

                dst_path = dst_dir + src_path[len(src_dir):]
                dst_file = src_file[:-len(type) - 1] + '.min' + src_file[-len(type) - 1:]

                src_filename = src_path + src_file
                dst_filename = dst_path + dst_file

                sys.stdout.write(src_filename)
                if not os.path.exists(dst_filename) or os.path.getmtime(src_filename) > os.path.getmtime(dst_filename): # source must be newer than destination
                    print '... '
                    os.system('java -jar yuicompressor-2.4.8.jar --type ' + type + ' -o "' + dst_filename + '" "' + src_filename + '"') # use YUICompressor
                    print '    ' + str(round(float(os.path.getsize(dst_filename)) / float(os.path.getsize(src_filename)) * 100.0, 1)) + '% ratio'
                else:
                    print ''

compress('css', 'styles/core/', '../core/resources/styles/')
compress('js', 'scripts/core/', '../core/resources/scripts/')

compress('css', 'styles/menu/', '../modules/menu/resources/styles/')
compress('js', 'scripts/menu/', '../modules/menu/resources/scripts/')

compress('js', 'scripts/groningenbijles/', '../themes/groningenbijles/resources/scripts/')

compress('css', 'styles/pure/', '../themes/pure/resources/styles/')
compress('js', 'scripts/pure/', '../themes/pure/resources/scripts/')

os.system('pause')