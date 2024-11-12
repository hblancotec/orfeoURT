##################################################################################
######## Adicionados de Django por Acappella #####################################
##################################################################################
import re

_datere = r'\d{4}-\d{1,2}-\d{1,2}'
_timere = r'(?:[01]?[0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?'
ansi_date_re = re.compile('^%s$' % _datere)
ansi_time_re = re.compile('^%s$' % _timere)
ansi_datetime_re = re.compile('^%s %s$' % (_datere, _timere))

def _isValidDate(date_string):
    """
    A helper function used by isValidANSIDate and isValidANSIDatetime to
    check if the date is valid.  The date string is assumed to already be in
    YYYY-MM-DD format.
    """
    from datetime import date
    # Could use time.strptime here and catch errors, but datetime.date below
    # produces much friendlier error messages.
    year, month, day = map(int, date_string.split('-'))
    # This check is needed because strftime is used when saving the date
    # value to the database, and strftime requires that the year be >=1900.
    if year < 1900:
        raise Exception, 'Year must be 1900 or later.'
    try:
        date(year, month, day)
    except ValueError, e:
        raise Exception, 'Invalid date: %s' % str(e)

def isValidANSIDate(field_data):
    if not ansi_date_re.search(field_data):
        raise Exception, 'Enter a valid date in YYYY-MM-DD format.'
    _isValidDate(field_data)

def isValidANSITime(field_data):
    if not ansi_time_re.search(field_data):
        raise Exception, 'Enter a valid time in HH:MM format.'

def isValidANSIDatetime(field_data):
    if not ansi_datetime_re.search(field_data):
        raise Exception, 'Enter a valid date/time in YYYY-MM-DD HH:MM format.'
    _isValidDate(field_data.split()[0])


from   functools  import *
import operator, re, types, string
import utils

from validate import *

# diccionario de validadores
validatorsDict__ = {}
utils_Register_Object("validatorsDict__",  validatorsDict__)

# validar valor en un rango de valores
def betWeen(value=None, min=None, max=None, inclusive=False):
    minComparator = operator.gt
    maxComparator = operator.lt
    if inclusive:
        minComparator = operator.ge
        maxComparator = operator.le
    comparators = []
    if min != None:        
        if not minComparator(value, min):
            return " {%s} menor que {%s}" % (value, min)
    if max != None:        
        if not maxComparator(value, max):
            return " {%s} mayor que {%s}" % (value, max)
    return False
validatorsDict__['BetWeen'] = betWeen

# valida que sea cadena de caracteres, y longitud, solo para caracteres
def stringLength(value=None, min=None, max=None):
    try:
        value = is_string(value)
    except Exception, e:
        return "Valor no es texto {%s}" % value
    
    result = betWeen(len(value), min, max, True)
    if result:
        return "Error longitud de cadena %s " % (result)
    
    return False    
validatorsDict__['StringLength'] = stringLength

# valida que sea mayor que
def greaterThan(value=None, limit=None, inclusive=False):
    result = betWeen(value=value, min=limit, max=None, inclusive=inclusive)
    if result:
       return "Error valor %s " % (result)
    return False    
validatorsDict__['GreaterThan'] = greaterThan

# valida que sea menor que
def lessThan(value=None, limit=None, inclusive=False):
    result = betWeen(value=value, min=None, max=limit, inclusive=inclusive)
    if result:
       return "Error valor %s " % (result)
    return False    
validatorsDict__['LessThan'] = lessThan

# v# valida que sea igual a
def equal(value=None, data=None):
    if not value == data:
       return " {%s} diferente a {%s}" % (value, data)
    return False    
validatorsDict__['Equal'] = equal

# valida que este en un arreglo de datos
def inArray(value=None, array=[]):
    if not value in array:
       return " {%s} no esta en %s" % (value, array)
    return False    
validatorsDict__['InArray'] = inArray

# valida que sea direccion email
def isEmail(value):
    if not re.match("^[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,6}$", value):
        return " {%s} no es direccion e-mail" % (value)
    return False
validatorsDict__['IsEmail'] = isEmail

# valida que no este vacio
def isEmpty(value):
    if value is None:
        return " {%s} Vacio" % (value)

    if ((type(value) == types.StringType) and (value == "")) or ((type(value) == types.UnicodeType) and (value == u"")):
        return " {%s} Vacio" % (value)

    if ((type(value) == types.IntType) or (type(value) == types.FloatType) or (type(value) == types.LongType)) and (value == 0):
       return " {%s} Vacio" % (value)    
    
    return False
validatorsDict__['IsEmpty'] = isEmpty

# valida que contenga solo alfabeto, solo caracteres
def alpha(value, extend="", blank=True):
    try:
        value = is_string(value)
    except:  
        return "Valor no es texto {%s}" % value
    
    novalid = []
    letters = string.ascii_letters + extend
    if blank:
        letters + " "
    for c in value:
        if (not (c in letters)):
            if not (c in novalid):
                novalid.append(c)
                
    if len(novalid) > 0:
        return "{%s} Con caracteres %s no contenidos en {%s} " % (value, novalid, letters)
    
    return False
validatorsDict__['Alpha'] = alpha

# valida que contenga solo alfanumerico
def digits(value):
    try:
        value = is_string(value)
    except:  
        return "Valor no es texto {%s}" % value
    
    novalid = []
    digits = string.digits   
    for c in value:
        if (not (c in digits)):
            if not (c in novalid):
                novalid.append(c)
                
    if len(novalid) > 0:
        return "{%s} Con caracteres %s no contenidos en {%s} " % (value, novalid, digits)
    
    return False
validatorsDict__['Digits'] = digits

# valida que contenga solo alfanumerico
def alphaNum(value, extend="", blank=True):
    try:
        value = is_string(value)
    except:  
        return "Valor no es texto {%s}" % value
    
    novalid = []
    digits  = string.ascii_letters + string.digits + extend
    
    if blank:
        digits + " "
        
    for c in value:
        if (not (c in digits)):
            if not (c in novalid):
                novalid.append(c)
                
    if len(novalid) > 0:
        return "{%s} Con caracteres %s no contenidos en {%s} " % (value, novalid, digits)
    
    return False
validatorsDict__['AlphaNum'] = alphaNum

# es flotante
def isFloat(value):
    try:
        value = is_float(value)        
    except:
        
        return "Valor no es flotante {%s}" % value
    
    return False
validatorsDict__['IsFloat'] = isFloat

# es entero
def isInteger(value):
    try:
        value = is_integer(value)        
    except:        
        return "Valor no es entero {%s}" % value
    
    return False
validatorsDict__['IsInteger'] = isInteger

# es fecha ansi
def isDate(value):
    try:
        value = isValidANSIDate(value)        
    except:        
        return "Valor no es fecha valida {%s}" % value
    
    return False
validatorsDict__['IsDate'] = isDate

# es tiempo ansi
def isTime(value):
    try:
        value = isValidANSITime(value)        
    except:        
        return "Valor no es hora valida {%s}" % value
    
    return False
validatorsDict__['IsTime'] = isTime

# es tiempo ansi
def isDateTime(value):
    try:
        value = isValidANSIDatetime(value)        
    except:        
        return "Valor no es fecha-hora valida {%s}" % value
    
    return False
validatorsDict__['IsDateTime'] = isDateTime