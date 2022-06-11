start = 'DROP PROCEDURE IF EXISTS'
delimiter = 'DELIMITER ;;'
delimiterEnd = 'DELIMITER ;'

def lindificar(proc):
    proc[1] = proc[1].replace(proc[1][7:proc[1].find('PROCEDURE')], '')
    proc[-2] = proc[-2].replace('END ;;', 'END $$')
    return proc

def parseProc(proc):
    if not len(proc):
        return False
    proc[0] = proc[0].replace('/*!50003 ', '').replace(' */', '')
    proc = lindificar(proc)
    proc = [proc[0], 'DELIMITER $$\n'] + proc[1:]
    resultado = {
        'nombre': proc[0].replace('DROP PROCEDURE IF EXISTS `', '').replace('`;\n', ''),
        'body': ''.join(proc)
    }
    return resultado

def main():
    procs = []
    with open('dump.sql', 'r') as dump:
        started = False
        ignore = False
        proc = []
        for line in dump.readlines():
            if start in line:
                started = True
                ignore = True
                proc.append(line)
            if started and not ignore:
                proc.append(line)
            if delimiter in line:
                ignore = False
            elif delimiterEnd in line:
                started = False
                ignore = False
                addProc = parseProc(proc)
                if addProc:
                    procs.append(addProc)
                proc = []
    for p in procs:
        with open(f'procedimientos/{p["nombre"]}.sql', 'w') as po:
            po.write(p['body'])
        



if __name__ == '__main__':
    main()