# transmission.py (utf-8)
# 
# Edited by: RR-DSE
# Timestamp: 22-01-13 16:43:23

# ------------
# Dependencies
# ------------

import fpdf
import unicodedata
import listools
import subprocess
import re
import datetime
import sys
import io
import os
import subprocess
import time
import shutil
import email
import email.encoders
import email.mime.base
import email.mime.multipart
import email.mime.text
import smtplib
import ssl
import urllib.request
import mysql.connector
import xml.etree.ElementTree as ElementTree
from copy import copy
import unicodedata
import random
import getpass
import math

# ---------
# Constants
# ---------

sCode = "utf-8"

bLogVerbose = False

sStandardDatetimeFormat = "%Y-%m-%d %H:%M:%S"
sStandardDateFormat = "%Y-%m-%d"
sDateFormat = "%d/%m/%Y"
sTimeFormat = "%H:%M"
sDatetimeFormat = "%d/%m/%Y %H:%M:%S"

sFileTimestampFormat = "%Y_%m_%d_%H_%M_%S"
sTimestampFormat = "%Y-%m-%d %H:%M:%S"

lsCc = []

sSubject = "#INSTITUTION#, #DEPARTMENT#, Resultados"

sConfigFolder = "config"
sSourceFolder = "update"
sOrderEncoding = "cp1252"
sOrderFile = "orders.txt"
sOrderRE = "^(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)\\|(.*)$"
sOrderDateRE = "^(\\d{4})(\\d{2})(\\d{2})$"
sOrderDateTimeRE = "^(\\d{4})(\\d{2})(\\d{2})(\\d{2})(\\d{2})$"
lsOrderTests = ["scov2", "scov2ag"]
sConfigXML = "config.xml"
sReportFolder = "results"
sLogFolder = "logs"
sAccessFolder = "accesstokens"

uMaxEmailDaysDiff = 5

# --------------
# Global objects
# --------------

dConfig = None

sHost = None
sUser = None
sPassword = None
sDatabase = None
sSMTPServer = None
uSMTPPort = None
sSMTPUser = None
sSMTPPassword = None
sSMTPSender = None

sUserID = None
uUserProfile = None
oSessionDatetime = None
uSessionTimeout = 600

dConfigReport = {
  "page_unit":  "mm",
  "page_format": "A4",
  "page_orientation": "P",
  "page_margin_left": 15,
  "page_margin_right": 15,
  "page_margin_top": 10,
  "page_margin_bottom": 10,
  "page_body_width": 178,
  "page_body_height": 277,
  "page_char_advance": 1.30,
  "page_header_sep": 2,
  "page_section_advance": 5,
  "page_section_sep": 2,
  "page_subsection_sep": 1.5,
  "font_family_header_1": "helvetica",
  "font_style_header_1": "",
  "font_size_header_1": 12,
  "font_height_header_1": 6,
  "font_sep_header_1": 1,
  "font_color_r_header_1": 80,
  "font_color_g_header_1": 80,
  "font_color_b_header_1": 80,
  "font_family_header_2": "helvetica",
  "font_style_header_2": "B",
  "font_size_header_2": 12,
  "font_height_header_2": 6,
  "font_sep_header_2": 1,
  "font_color_r_header_2": 0,
  "font_color_g_header_2": 0,
  "font_color_b_header_2": 0,
  "font_family_title_1": "helvetica",
  "font_style_title_1": "B",
  "font_size_title_1": 14,
  "font_height_title_1": 5,
  "font_sep_title_1": 2,
  "font_color_r_title_1": 0,
  "font_color_g_title_1": 0,
  "font_color_b_title_1": 0,
  "font_family_section_1": "helvetica",
  "font_style_section_1": "B",
  "font_size_section_1": 8,
  "font_height_section_1": 5,
  "font_sep_section_1": 1,
  "font_color_r_section_1": 0,
  "font_color_g_section_1": 0,
  "font_color_b_section_1": 0,
  "font_family_section_3": "helvetica",
  "font_style_section_3": "B",
  "font_size_section_3": 7,
  "font_height_section_3": 4,
  "font_sep_section_3": 0,
  "font_color_r_section_3": 0,
  "font_color_g_section_3": 0,
  "font_color_b_section_3": 0,
  "font_family_normal": "helvetica",
  "font_style_normal": "",
  "font_size_normal": 11,
  "font_height_normal": 5,
  "font_sep_normal": 0,
  "font_color_r_normal": 0,
  "font_color_g_normal": 0,
  "font_color_b_normal": 0,
  "font_family_normal_bold": "helvetica",
  "font_style_normal_bold": "B",
  "font_size_normal_bold": 11,
  "font_height_normal_bold": 5,
  "font_sep_normal_bold": 0,
  "font_color_r_normal_bold": 0,
  "font_color_g_normal_bold": 0,
  "font_color_b_normal_bold": 0,
  "font_family_small": "helvetica",
  "font_style_small": "",
  "font_size_small": 10,
  "font_height_small": 4,
  "font_sep_small": 0,
  "font_color_r_small": 0,
  "font_color_g_small": 0,
  "font_color_b_small": 0,
  "font_family_small_bold": "helvetica",
  "font_style_small_bold": "B",
  "font_size_small_bold": 10,
  "font_height_small_bold": 4,
  "font_sep_small_bold": 0,
  "font_color_r_small_bold": 0,
  "font_color_g_small_bold": 0,
  "font_color_b_small_bold": 0,
  "font_family_small_2": "helvetica",
  "font_style_small_2": "",
  "font_size_small_2": 9,
  "font_height_small_2": 3.5,
  "font_sep_small_2": 0,
  "font_color_r_small_2": 0,
  "font_color_g_small_2": 0,
  "font_color_b_small_2": 0,
  "font_family_small_2_bold": "helvetica",
  "font_style_small_2_bold": "B",
  "font_size_small_2_bold": 9,
  "font_height_small_2_bold": 3.5,
  "font_sep_small_2_bold": 0,
  "font_color_r_small_2_bold": 0,
  "font_color_g_small_2_bold": 0,
  "font_color_b_small_2_bold": 0
}

dMySqlDB = None
dMySqlCursor = None

oSSLContext = None
oSMTPServer = None
oLogTimeStamp = None

dMemLog = io.StringIO(newline=None)
dTests = dict()
ldEmails = []

uGroupListPageLen = 5

# -----------
# Log methods
# -----------

def Log(sText):
  global dMemLog, bLogVerbose, oLogTimeStamp
  if not oLogTimeStamp:
    oLogTimeStamp = datetime.datetime.now()
  dMemLog.write(sText + os.linesep)
  if bLogVerbose:
    print(sText)

def SaveLog():
  global dMemLog, sLogFolder, sFileTimestampFormat, oLogTimeStamp
  if not oLogTimeStamp:
    oLogTimeStamp = datetime.datetime.now()
  dMemLog.seek(0)
  sTimeStamp = oLogTimeStamp.strftime(sFileTimestampFormat)
  dFile = open("{}\\{}".format(sLogFolder, sTimeStamp + "_transmission.log"), "w", encoding = "utf-8")
  dFile.write(dMemLog.read())
  dFile.close()

# -----------------
# Auxiliary methods
# -----------------

def StrLatin1(sInput):
  sRes = sInput.encode("latin-1", errors="replace").decode("latin-1")
  return sRes

def IsInt(sInput):
  try:
    nValue = int(sInput)
  except:
    return False
  return True

def GetGreeting():
  uHour = datetime.datetime.now().hour
  if uHour >= 20 or uHour < 6:
    return "Boa noite."
  elif uHour >= 12 and uHour < 20:
    return "Boa tarde."
  else:
    return "Bom dia."

def ConvertDatetime(sInput, sFormat, sDestFormat):
  return datetime.datetime.strptime(sInput, sFormat).strftime(sDestFormat)

def Exit():
  global oSMTPServer, sLogFolder, oLogTimeStamp, sFileTimestampFormat
  if oSMTPServer:
    EmailLogout()
  SaveLog()
  if oLogTimeStamp:
    sTimeStamp = oLogTimeStamp.strftime(sFileTimestampFormat)
    #os.startfile("{}\\{}".format(sLogFolder, sTimeStamp + "_transmissao.log"))
  sys.exit()

def LISGetReport(sSample, sFolder = None):
  global sReportFolder
  sActualFolder = sReportFolder
  if sFolder and sFolder != "":
    sActualFolder = sFolder
  try:
    sURL = "#INTRANET_WEB_SERVICE_1#".format(sSample)
    byURLRequest = urllib.request.urlopen(sURL).read()
    sURL = "#INTRANET_WEB_SERVICE_2#_{}.pdf".format(sSample)
    oURLRetrieve = urllib.request.urlretrieve(sURL, "{}\\{}.pdf".format(sActualFolder, sSample))
  except:
    if not os.path.exists("{}\\{}.pdf".format(sActualFolder, sSample)):
      Log("")
      Log("Erro: Nao foi possivel descarregar o boletim para a amostra {}.".format(sSample))
      raise

def OpenPDF(sFile, bFavourAcrobatReader = True):
  global bUseAcrobatReader, sAcrobatReaderCommand
  if bUseAcrobatReader and bFavourAcrobatReader:
    subprocess.Popen([sAcrobatReaderCommand, "/p", sFile], creationflags = subprocess.DETACHED_PROCESS)
  else:
    os.startfile(sFile)

def PrintResult(sSample):
  global sReportFolder
  try:
    LISGetReport(sSample)
    OpenPDF("{}\\{}.pdf".format(sReportFolder, sSample), False)
  except:
    raise

def RemoveAccents(sInput):
  sNFKD = unicodedata.normalize('NFKD', sInput)
  sRes = u"".join([c for c in sNFKD if not unicodedata.combining(c)])
  sRes = sRes.lower()
  sRes = listools.CleanStr(sRes)
  return sRes

def CleanStrName(sSrc):
  sRes = re.sub(r"^\s+", "", str(sSrc))
  sRes = re.sub(r"\s+$", "", sRes)
  sRes = re.sub("\'|\"|\^|\\.", "", sRes)
  sRes = re.sub(r"\s+", " ", sRes)
  return sRes

def CleanStr(sSrc):
  sRes = re.sub(r"^\s+", "", str(sSrc))
  sRes = re.sub(r"\s+$", "", sRes)
  sRes = re.sub("\'|\"|\^|\\.", " ", sRes)
  sRes = re.sub(r"\s+", " ", sRes)
  return sRes

def GetResult(sInput):
  sResult = "Erro"
  dResTrans = {
    'negative': "Nao detetado",
    'positive': "Detetado",
    'error': "Erro",
    'inconclusive': "Inconclusivo",
    'waiting': "Pendente"
     }
  if sInput in dResTrans:
    sResult = dResTrans[sInput]
  return sResult

def GetTransmissionClass(sInput):
  sResult = "Erro"
  dResTrans = {
    'email': "@",
    'phone': "T",
    'print': "I",
    'internal': "U",
    'unknown': "D"
     }
  if sInput in dResTrans:
    sResult = dResTrans[sInput]
  return sResult

def GetGroupClass(sInput):
  sResult = "Erro"
  dResTrans = {
    'nursing_home': "ERPI",
    'school': "Instituicao de ensino",
    'children_home': "Lar infantil",
    'healthcare_unit': "Unidade de saude",
    'other': "Outra"
     }
  if sInput in dResTrans:
    sResult = dResTrans[sInput]
  return sResult

def GetGroupClass2(sInput):
  sResult = "Erro"
  dResTrans = {
    'nursing_home': "ERPI",
    'school': "instituicao de ensino",
    'children_home': "lar infantil",
    'healthcare_unit': "unidade de saude",
    'other': "outra instituicao"
     }
  if sInput in dResTrans:
    sResult = dResTrans[sInput]
  return sResult

def GetGroupPatientClass(sInput):
  sResult = "Erro"
  dResTrans = {
    'patient': "Utente",
    'collaborator': "Colaborador",
    'internal': "Interno",
    'contact': "Contacto"
     }
  if sInput in dResTrans:
    sResult = dResTrans[sInput]
  return sResult

def GetGroupPatientClassPlural(sInput):
  sResult = "Erro"
  dResTrans = {
    'patient': "Utentes",
    'collaborator': "Colaboradores",
    'internal': "Internos",
    'contact': "Contactos"
     }
  if sInput in dResTrans:
    sResult = dResTrans[sInput]
  return sResult

def GetRecipientClass(sInput):
  sResult = "Erro"
  dResTrans = {
    'patient': "proprio",
    'clinician': "clinico",
    'manager': "institucional",
    'caregiver': "cuidador",
    'family': "familiar",
    'unknown': "desconhecido"
     }
  if sInput in dResTrans:
    sResult = dResTrans[sInput]
  return sResult

def SearchEmails(sName, sBirthday, sStateID, sDate):
  global ldEmails, sStandardDateFormat, uMaxEmailDaysDiff
  lNull = [None, "", "null", "NULL", "Null"]
  ldRes = list()
  sPattern1 = ".*"+".*".join(CleanStrName(RemoveAccents(sName)).split())+".*"
  for dEmail in ldEmails:
    bFound = False
    sPattern2 = ".*"+".*".join(dEmail['name'].split())+".*"
    if dEmail['state_id'] not in lNull and sStateID not in lNull and dEmail['state_id'].upper() == sStateID.upper():
      bFound = True
    else:
      if (re.match(sPattern1, dEmail['name']) or re.match(sPattern2, CleanStrName(RemoveAccents(sName)))) and dEmail['birthday'] == sBirthday:
        bFound = True
    if bFound:
      oDate1 = datetime.datetime.strptime(sDate, sStandardDateFormat)
      oDate2 = datetime.datetime.strptime(dEmail['date'], sStandardDateFormat)
      if abs((oDate1 - oDate2).days) > uMaxEmailDaysDiff:
        bFound = False
    if bFound:
      ldRes.append({
        'email': dEmail['email'],
        'class': dEmail['class']
        })
  return ldRes

def GeneratePassword():
  lsCharacters = ["A", "B", "C", "D", "E", "F", "G", "H", "J", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "X", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9"]
  uPassLen = 8
  try:
    bFound = False
    while not bFound:
      sRes = ""
      for uIndex in range(0, uPassLen):
        sRes = sRes + lsCharacters[int(random.random() * len(lsCharacters))]
      sQuery = "SELECT id FROM access_control WHERE UPPER(password) = {}".format(GetDBString(sRes.upper()))
      dIDs = RunMySql(sQuery, True)
      if len(dIDs) == 0:
        bFound = True
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError
  return sRes

# -----------
# PDF methods
# -----------

def StartReport():
  global oReport, dConfigReport
  oReport = fpdf.FPDF(
    orientation = dConfigReport['page_orientation'],
    unit = dConfigReport['page_unit'],
    format = dConfigReport['page_format'])
  oReport.set_margins(
    dConfigReport['page_margin_left'],
    dConfigReport['page_margin_top'],
    dConfigReport['page_margin_right'])
  return

def AddPage():
  global oReport, dConfigReport
  oReport.add_page()
  return

def SetFont(sStyle):
  global oReport, dConfigReport
  oReport.set_font(
    dConfigReport["font_family_" + sStyle.lower()] if dConfigReport["font_family_" + sStyle.lower()] != None else "",
    dConfigReport["font_style_" + sStyle.lower()] if dConfigReport["font_style_" + sStyle.lower()] != None else "",
    dConfigReport["font_size_" + sStyle.lower()] if dConfigReport["font_size_" + sStyle.lower()] != None else 1)
  oReport.set_text_color(
    dConfigReport["font_color_r_" + sStyle.lower()] if dConfigReport["font_color_r_" + sStyle.lower()] != None else 0,
    dConfigReport["font_color_g_" + sStyle.lower()] if dConfigReport["font_color_g_" + sStyle.lower()] != None else 0,
    dConfigReport["font_color_b_" + sStyle.lower()] if dConfigReport["font_color_b_" + sStyle.lower()] != None else 0)
  return

def SetPos(fX, fY):
  global oReport, dConfigReport
  oReport.set_xy(dConfigReport['page_margin_left'] + fX, dConfigReport['page_margin_top'] + fY)
  return

def GetPos():
  global oReport, dConfigReport
  fCurrX = oReport.get_x() - dConfigReport['page_margin_left']
  fCurrY = oReport.get_y() - dConfigReport['page_margin_top']
  return (fCurrX, fCurrY)

def MovePos(fX, fY):
  global oReport, dConfigReport
  fCurrX = oReport.get_x()
  fCurrY = oReport.get_y()
  oReport.set_xy(fCurrX + fX, fCurrY + fY)
  return GetPos()

def GetStringWidth(sText, sStyle = None):
  global oReport, dConfigReport
  if sStyle:
    SetFont(sStyle)
  return oReport.get_string_width(sText)

def Write(sText, sStyle):
  global oReport, dConfigReport
  SetFont(sStyle)
  oReport.write(dConfigReport["font_height_" + sStyle.lower()], StrLatin1(sText))
  return

def WriteLine(sText, sStyle, uAdvance = 0, fPosX = 0.0):
  global oReport, dConfigReport
  MovePos(dConfigReport["page_section_advance"] * float(uAdvance) + fPosX, 0.0)
  Write(sText + "\n", sStyle)
  MovePos(0.0, dConfigReport["font_sep_" + sStyle.lower()])
  return

def WriteSep(sStyle, uAdvance = 0, fPosX = 0.0):
  global oReport, dConfigReport
  MovePos(dConfigReport["page_section_advance"] * float(uAdvance) + fPosX, 0.0)
  MovePos(0.0, dConfigReport["font_height_" + sStyle.lower()])
  return

def DrawCell(sText, sStyle, fWidth, fHeight, sAlign, sBorder, bFill = False, uAdvance = 0, fPosX = 0.0, tuFillColor = None):
  global oReport, dConfigReport
  if not fHeight:
    fActualHeight = dConfigReport["font_height_" + sStyle.lower()]
  else:
    fActualHeight = fHeight
  MovePos(dConfigReport["page_section_advance"] * float(uAdvance) + fPosX, 0.0)
  SetFont(sStyle)
  if bFill:
    if tuFillColor:
      oReport.set_fill_color(tuFillColor[0], tuFillColor[1], tuFillColor[2])
  oReport.cell(fWidth, h = fActualHeight, txt = StrLatin1(sText), border = sBorder.upper(), ln = 1, align = sAlign, fill = bFill)
  return

def DrawMultiCell(sText, sStyle, fWidth, fHeight, sAlign, sBorder, bFill = False, uAdvance = 0, fPosX = 0.0, tuFillColor = None):
  global oReport, dConfigReport
  if not fHeight:
    fActualHeight = dConfigReport["font_height_" + sStyle.lower()]
  else:
    fActualHeight = fHeight
  MovePos(dConfigReport["page_section_advance"] * float(uAdvance) + fPosX, 0.0)
  SetFont(sStyle)
  if bFill:
    if tuFillColor:
      oReport.set_fill_color(tuFillColor[0], tuFillColor[1], tuFillColor[2])
  oReport.multi_cell(fWidth, h = fActualHeight, txt = StrLatin1(sText), border = sBorder.upper(), align = sAlign, fill = bFill)
  return

def DrawRule(fWidth, sStyle = None):
  global oReport, dConfigReport
  fActualX1 = oReport.get_x() + dConfigReport['page_char_advance']
  fActualY1 = oReport.get_y()
  fActualX2 = fActualX1 + fWidth
  fActualY2 = fActualY1
  oReport.line(fActualX1, fActualY1, fActualX2, fActualY2)
  if sStyle:
    MovePos(0.0, dConfigReport["font_sep_" + sStyle.lower()])
  return

def DrawLine(fX1, fY1, fX2, fY2):
  global oReport, dConfigReport
  if not fX1:
    fActualX1 = oReport.get_x() + dConfigReport['page_char_advance']
  else:
    fActualX1 = dConfigReport['page_margin_left'] + fX1 + dConfigReport['page_char_advance']
  if not fY1:
    fActualY1 = oReport.get_y()
  else:
    fActualY1 = dConfigReport['page_margin_top'] + fY1
  if not fX2:
    fActualX2 = oReport.get_x() + dConfigReport['page_char_advance']
  else:
    fActualX2 = dConfigReport['page_margin_left'] + fX2 + dConfigReport['page_char_advance']
  if not fY2:
    fActualY2 = oReport.get_y()
  else:
    fActualY2 = dConfigReport['page_margin_top'] + fY2
  oReport.line(fActualX1, fActualY1, fActualX2, fActualY2)
  return

def DrawCircle(fX1, fY1, fRadius):
  global oReport, dConfigReport
  if not fX1:
    fActualX1 = oReport.get_x() + dConfigReport['page_char_advance']
  else:
    fActualX1 = dConfigReport['page_margin_left'] + fX1 + dConfigReport['page_char_advance']
  if not fY1:
    fActualY1 = oReport.get_y()
  else:
    fActualY1 = dConfigReport['page_margin_top'] + fY1
  oReport.ellipse(fActualX1 - fRadius, fActualY1 - fRadius, fRadius * 2.0, fRadius * 2.0)
  return

def AddImage(sPath, fX, fY, fWidth, fHeight, sType):
  global oReport, dConfigReport
  oReport.image(sPath, fX, fY, fWidth, fHeight, sType, "")
  return

def CreateAccessInfo(sToken, sPassword):
  global oReport, dConfigReport, sDateFormat, sTimeFormat, sAccessFolder
  oNow = datetime.datetime.now()
  fBodyXStart = 0.0
  fBulletXStart = 10.0
  fBodyWidth = dConfigReport['page_body_width'] - fBodyXStart * 2.0
  uPageCount = 1
  StartReport()
  if sToken.upper() == "VOID":
    uPageCount = 2
  for uPage in range(0, uPageCount):
    AddPage()
    SetPos(0,0)
    WriteLine("#INSTITUTION#", "header_1")
    WriteLine("#DEPARTMENT#", "header_2")
    MovePos(0.0, 2 * dConfigReport['page_header_sep'])
    WriteLine("Resultado de teste para SARS-CoV-2 (COVID-19)", "title_1")
    DrawRule(dConfigReport['page_body_width'], None)
    MovePos(0.0, dConfigReport['page_header_sep'])
    WriteSep("small_2")
    DrawMultiCell("Recentemente, uma amostra sua foi colhida para realização de um teste clínico para a COVID-19 no laboratório do #DEPARTMENT# da #INSTITUTION#.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    DrawMultiCell("O #DEPARTMENT# espera ter o seu teste concluído entre 24 a 48h. Poderá, depois, consultar o resultado do seu teste na #STATE_PORTAL_3#, no seguinte endereço:", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    DrawCell("https://example_website/example", "normal_bold", 0, None, "L", "", False, 0, fBulletXStart)
    WriteSep("small_2")
    DrawMultiCell("Se tiver dificuldades em aceder à #STATE_PORTAL_3#, poderá solicitar o resultado do seu teste ao #DEPARTMENT#, por email ou contacto telefónico, fazendo uso dos seguintes dados pessoais para acesso ao resultado:", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    MovePos(fBulletXStart, 0)
    if sToken.upper() == "VOID":
      Write("Nome: ", "normal_bold")
      Write("________________________________________", "normal")
      Write(" DN: ", "normal_bold")
      WriteLine("___/___/_____", "normal")
      WriteSep("small_2")
      MovePos(fBulletXStart, 0)
      Write("N.º utente: ", "normal_bold")
      Write("____________  ", "normal")
    else:
      Write("Amostra: ", "normal_bold")
      WriteLine("{}".format(sToken), "normal")
      MovePos(fBulletXStart, 0)
    Write("Senha de acesso*: ", "normal_bold")
    WriteLine("{}".format(sPassword), "normal")
    if sToken.upper() != "VOID":
      MovePos(fBulletXStart, 0)
      WriteLine("Nome completo, data de nascimento e #STATE_ID_1#", "normal_bold")
    WriteSep("small_2")
    MovePos(fBulletXStart, 0)
    WriteLine("*Nota: Esta senha de acesso só serve para as comunicações com o #DEPARTMENT#.", "small_2")
    WriteSep("small_2")
    DrawMultiCell("Se utilizar o email, inclua na mensagem os dados pessoais para acesso ao resultado (incluindo a senha de acesso). Refira no assunto \"pedido de resultado de teste para a COVID-19\" e envie a mensagem para o seguinte endereço:", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    DrawCell("example@email.com", "normal_bold", 0, None, "L", "", False, 0, fBulletXStart)
    WriteSep("small_2")
    DrawMultiCell("Por esta via de comunicação, o #DEPARTMENT# enviará o resultado, por defeito, para o endereço usado como remetente.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    DrawMultiCell("Solicitamos que utilize o contacto telefónico para receber o resultado do seu teste apenas se tiver dificuldades em utilizar meios informáticos. Para este efeito, tem à sua disposição, nos dias úteis, entre as 9 e as 17h, o número de telefone 937891713. Nestas situações, o #DEPARTMENT# irá solicitar-lhe os dados pessoais para acesso ao resultado, que deverá ter à mão.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    DrawMultiCell("Se tiver dúvidas, ou outras questões, poderá contactar o #DEPARTMENT# pelas mesmas vias.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    WriteSep("small_2")
    DrawMultiCell("Agradecemos a sua colaboração.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    WriteSep("small_2")
    WriteLine("Esta informação foi impressa a {} às {}".format(oNow.strftime(sDateFormat), oNow.strftime(sTimeFormat)), "small_2")
    WriteSep("small_2")
    WriteSep("small_2")
    WriteSep("small_2")
    DrawMultiCell("AVISO DE CONFIDENCIALIDADE: Esta informação e os dados para acesso ao resultado nela constantes são confidenciais e pessoais, sendo o seu sigilo protegido por lei. Se não for o destinatário ou pessoa autorizada a receber esta informação, não pode usar, copiar ou divulgar as informações nela contidas ou tomar qualquer ação baseada nas mesmas. Se for este o caso, proceda imediatamente à sua destruição, notificando o #DEPARTMENT# da #INSTITUTION#.. Agradecemos a sua cooperação.", "small", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
    WriteSep("small_2")
    DrawMultiCell("LIMITAÇÃO DE RESPONSABILIDADE: A informação aqui presente contém e permite o acesso a dados pessoais e confidenciais. Se perder esta informação e se for o seu destinatário, contacte imediatamente o #DEPARTMENT# da #INSTITUTION#. Este departamento não se responsabiliza por quaisquer factos suscetíveis de afetar a integridade de dados pessoais e das respetivas consequências que advenham da utilização indevida da informação aqui presente, incluindo, mas não se limitando, ao seu acesso por terceiros ou ao uso de endereços de email incorretos ou de terceiros.", "small", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  oReport.output("{}\\{}.pdf".format(sAccessFolder, sToken if sToken.upper() != "VOID" else sPassword.upper()))
  OpenPDF("{}\\{}.pdf".format(sAccessFolder, sToken if sToken.upper() != "VOID" else sPassword.upper()), False)

def CreateAccessInfo_NoToken():
  global oReport, dConfigReport, sDateFormat, sTimeFormat, sAccessFolder
  oNow = datetime.datetime.now()
  fBodyXStart = 0.0
  fBulletXStart = 10.0
  fBodyWidth = dConfigReport['page_body_width'] - fBodyXStart * 2.0
  StartReport()
  AddPage()
  SetPos(0,0)
  WriteLine("#INSTITUTION#", "header_1")
  WriteLine("#DEPARTMENT#", "header_2")
  MovePos(0.0, 2 * dConfigReport['page_header_sep'])
  WriteLine("Resultado de teste para SARS-CoV-2 (COVID-19)", "title_1")
  DrawRule(dConfigReport['page_body_width'], None)
  MovePos(0.0, dConfigReport['page_header_sep'])
  WriteSep("small_2")
  DrawMultiCell("Recentemente, uma amostra sua foi colhida para realização de um teste clínico para a COVID-19 no laboratório do #DEPARTMENT# da #INSTITUTION#.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  DrawMultiCell("O #DEPARTMENT# espera ter o seu teste concluído entre 24 a 48h. Poderá, depois, consultar o resultado do seu teste na #STATE_PORTAL_3#, no seguinte endereço:", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  DrawCell("https://example_website/example", "normal_bold", 0, None, "L", "", False, 0, fBulletXStart)
  WriteSep("small_2")
  DrawMultiCell("Se tiver dificuldades em aceder à #STATE_PORTAL_3#, poderá solicitar o resultado do seu teste ao #DEPARTMENT#, por email, no seguinte endereço:", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  DrawCell("example@email.com", "normal_bold", 0, None, "L", "", False, 0, fBulletXStart)
  WriteSep("small_2")
  DrawMultiCell("Para acesso ao resultado por email, refira no assunto \"pedido de resultado de teste para a COVID-19\" e indique no corpo da mensagem os seguintes dados pessoais:", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  MovePos(fBulletXStart, 0)
  WriteLine("Nome completo", "normal_bold")
  MovePos(fBulletXStart, 0)
  WriteLine("Data de nascimento", "normal_bold")
  MovePos(fBulletXStart, 0)
  WriteLine("#STATE_ID_1#", "normal_bold")
  MovePos(fBulletXStart, 0)
  WriteLine("#STATE_ID_2#", "normal_bold")
  WriteSep("small_2")
  DrawMultiCell("Caso não tenha #STATE_DOC_1#, deve indicar o número de #STATE_DOC_2# ou de #STATE_DOC_3#.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  DrawMultiCell("Por email, o #DEPARTMENT# enviará o resultado, por defeito, para o endereço usado como remetente.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  WriteSep("small_2")
  DrawMultiCell("Agradecemos a sua colaboração.", "normal", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  WriteSep("small_2")
  WriteSep("small_2")
  WriteLine("Esta informação foi impressa a {} às {}".format(oNow.strftime(sDateFormat), oNow.strftime(sTimeFormat)), "small_2")
  WriteSep("small_2")
  WriteSep("small_2")
  WriteSep("small_2")
  DrawMultiCell("AVISO DE CONFIDENCIALIDADE: As mensagens de correio eletrónico com resultados de análises clínicas contêm informações confidenciais, sendo o seu sigilo protegido por lei. Se não for o destinatário ou pessoa autorizada a receber estas mensagens, não pode usar, copiar ou divulgar as informações nela contidas ou tomar qualquer ação baseada nas mesmas e deverá proceder imediatamente à sua destruição, notificando o remetente. Agradecemos a sua cooperação.", "small", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  DrawMultiCell("LIMITAÇÃO DE RESPONSABILIDADE: A segurança da transmissão de resultados de análises clínicas por via electrónica não pode ser garantida pelo remetente, o qual, em, consequência, não se responsabiliza por quaisquer factos suscetíveis de afectar a sua integridade e a dos restantes dados pessoais nela contidas, incluindo, mas não se limitando, ao seu acesso indevido por terceiros.", "small", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  DrawMultiCell("CONFIDENTIALITY NOTICE: E-mail messages with clinical test results contain confidential information, and their confidentiality is protected by law. If you are not the recipient or person authorized to receive these messages, you may not use, copy or disclose the information contained therein or take any action based on them and you must immediately proceed with their destruction, notifying the sender. We appreciate your cooperation.", "small", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  WriteSep("small_2")
  DrawMultiCell("DISCLAIMER: The security of electronical transmissions of clinical test results cannot be guaranteed by the sender, who, consequently, does not accept liability for any facts that may affect their integrity and that of the other personal data contained therein, including, but not limited to, their improper access by third parties.", "small", fBodyWidth, None, "J", "", False, 0, fBodyXStart)
  oReport.output("{}\\{}.pdf".format(sAccessFolder, "no_token"))
  OpenPDF("{}\\{}.pdf".format(sAccessFolder, "no_token"))

# ----------------
# Database methods
# ----------------

def MySqlCursorStart():
  global dMySqlDB, dMySqlCursor
  global sHost, sUser, sPassword, sDatabase
  dMySqlDB = mysql.connector.connect(host = sHost, user = sUser, passwd = sPassword, database = sDatabase)
  dMySqlCursor = dMySqlDB.cursor(dictionary = True)
  return

def MySqlCursorClose():
  global dMySqlDB, dMySqlCursor
  dMySqlCursor.close()
  dMySqlDB.close()
  return

def RunMySqlCursor(sSQLQuery, bFetch = False):
  global dMySqlCursor, sDateFormat, sStandardDatetimeFormat
  dRes = list()
  dMySqlCursor.execute(sSQLQuery)
  if dMySqlCursor.rowcount == 0 or not bFetch:
    return dRes
  else:
    dRes = dMySqlCursor.fetchall()
    CommitMySql()
    for dRow in dRes:
      for sKey in dRow:
        if isinstance(dRow[sKey], int):
          dRow[sKey] = str(dRow[sKey])
        elif isinstance(dRow[sKey], float):
          dRow[sKey] = str(int(dRow[sKey]))
        elif isinstance(dRow[sKey], datetime.datetime):
          dRow[sKey] = dRow[sKey].strftime(sStandardDatetimeFormat)
        elif isinstance(dRow[sKey], datetime.date):
          dRow[sKey] = dRow[sKey].strftime(sDateFormat)
        elif dRow[sKey] == None:
          dRow[sKey] = "NULL"
        else:
          dRow[sKey] = str(dRow[sKey])
  return dRes

def RunMySqlBatch(sSQLQuery, bFetch = False):
  global sHost, sUser, sPassword, sDatabase, sCode
  sCmd = "mysql -h {0} -u {1} -p{2} {3} -B -e \"{4}\"".format(sHost, sUser, sPassword, sDatabase, sSQLQuery)
  lsLines = subprocess.check_output(sCmd, stderr=subprocess.DEVNULL).decode(sCode).splitlines()
  if len(lsLines) == 0:
    return list()
  lsFields = lsLines[0].split("\t")
  ldRes = list()
  for sLine in lsLines[1:]:
    lsLine = sLine.split("\t")
    ldNew = dict()
    uIndex = 0
    for sField in lsLine:
      ldNew[lsFields[uIndex]] = sField
      uIndex = uIndex + 1
    ldRes.append(ldNew)
  return ldRes

def RunMySql(sSQLQuery, bFetch = False):
  #return RunMySqlBatch(sSQLQuery, bFetch)
  return RunMySqlCursor(sSQLQuery, bFetch)

def CommitMySql():
  global dMySqlDB
  if dMySqlDB:
    dMySqlDB.commit()

def GetDBString(sValue):
  if sValue == "NULL" or sValue == "null" or not sValue:
    return "NULL"
  else:
    return "'{}'".format(sValue)

def DBInsertTransmission(sTestID, sClass, sDescription, sRecipientClass, sRecipientDescription, sStatus):
  global sUserID
  try:
    sQuery = """
      SELECT
        id
      FROM transmissions
      WHERE 
        sample_id = {}
        AND class = {}
        AND description = {}
      """.format(
      GetDBString(sTestID),
      GetDBString(sClass),
      GetDBString(sDescription)
    )
    dTransmissions = RunMySql(sQuery, True)
    bFound = False
    if len(dTransmissions) > 0:
      bFound = True
    if bFound:
      sID = dTransmissions[0]['id']
      sQuery = "INSERT audit_transmissions SELECT * FROM transmissions WHERE id={}".format(sID)
      RunMySql(sQuery, False)
      sQuery = """
        UPDATE transmissions
        SET
          recipient_class = {},
          recipient_description = {},
          status = {},
          mod_user = {},
          mod_datetime = NOW()
        WHERE
          id = {}
      """.format(
        GetDBString(sRecipientClass),
        GetDBString(sRecipientDescription),
        GetDBString(sStatus),
        sUserID,
        GetDBString(sID)
      )
    else:
      sQuery = """
        INSERT INTO transmissions VALUES
        (
          NULL,
          {},
          {},
          {},
          {},
          {},
          NOW(),
          {},
          {},
          NOW(),
          {},
          NOW()
        )
        """.format(
        sTestID,
        GetDBString(sClass),
        GetDBString(sDescription),
        GetDBString(sRecipientClass),
        GetDBString(sRecipientDescription),
        GetDBString(sStatus),
        sUserID,
        sUserID
        )
    RunMySql(sQuery, False)
    CommitMySql()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'transmissions' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError

def DBUpdateTransmission(sID, sClass, sDescription, sRecipientClass, sRecipientDescription, sStatus):
  global sUserID
  try:
    sQuery = "INSERT audit_transmissions SELECT * FROM transmissions WHERE id={}".format(sID)
    RunMySql(sQuery, False)
    sQuery = """
      UPDATE transmissions
      SET
        class = {},
        description = {},
        recipient_class = {},
        recipient_description = {},
        status = {},
        mod_user = {},
        mod_datetime = NOW()
      WHERE
        id = {}
    """.format(
      GetDBString(sClass),
      GetDBString(sDescription),
      GetDBString(sRecipientClass),
      GetDBString(sRecipientDescription),
      GetDBString(sStatus),
      sUserID,
      GetDBString(sID)
    )
    RunMySql(sQuery, False)
    CommitMySql()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'transmissions' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError

def DBDeleteTransmission(sID):
  global sUserID
  try:
    sQuery = "INSERT audit_transmissions SELECT * FROM transmissions WHERE id={}".format(sID)
    RunMySql(sQuery, False)
    sQuery = """
      DELETE FROM transmissions
      WHERE id = {}
      """.format(GetDBString(sID))
    RunMySql(sQuery, False)
    CommitMySql()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'transmissions' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError

def DBResetAccessControl():
  try:
    sQuery = """
      DROP TABLE IF EXISTS access_control
      """
    RunMySql(sQuery, False)
    sQuery = """
      CREATE TABLE access_control
      (
        id SERIAL,
        token CHAR(32) NOT NULL,
        sample_id CHAR(32) NOT NULL,
        password CHAR(16) NOT NULL,
        "datetime" DATETIME NOT NULL,
        user BIGINT(20) UNSIGNED NOT NULL
      )
      """
    RunMySql(sQuery, False)
    CommitMySql()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError

def DBInsertAccessControl(sSampleID, sToken):
  global sUserID
  dRes = dict()
  try:
    sQuery = """
      SELECT
        id AS ID,
        password AS Password
      FROM access_control
      WHERE 
        UPPER(sample_id) = {}
      """.format(
      GetDBString(sSampleID.strip().upper())
    )
    dAccessControls = RunMySql(sQuery, True)
    bFound = False
    if len(dAccessControls) > 0:
      bFound = True
    if bFound:
      uID = dAccessControls[0]['ID']
      sPassword = dAccessControls[0]['Password']
      sQuery = """
        UPDATE access_control
        SET
          token = {},
          "datetime" = NOW(),
          user = {}
        WHERE
          id = {}
      """.format(
        GetDBString(sToken.strip().upper()),
        sUserID,
        uID
      )
      dRes['token'] = sToken.strip().upper()
      dRes['sample_id'] = sSampleID.strip().upper()
      dRes['password'] = sPassword
    else:
      sPassword = GeneratePassword()
      sQuery = """
        INSERT INTO access_control VALUES (
          NULL, 
          {},
          {},
          {},
          NOW(),
          {}
        )
      """.format(
        GetDBString(sToken.strip().upper()),
        GetDBString(sSampleID.strip().upper()),
        GetDBString(sPassword),
        sUserID
      )
      dRes['token'] = sToken.strip().upper()
      dRes['sample_id'] = sSampleID.strip().upper()
      dRes['password'] = sPassword
    RunMySql(sQuery, False)
    CommitMySql()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError
  return dRes

def DBInsertAccessControlUnknown():
  global sUserID
  dRes = dict()
  try:
    sPassword = GeneratePassword()
    sQuery = """
      INSERT INTO access_control VALUES (
        NULL, 
        'VOID',
        'VOID',
        {},
        NOW(),
        {}
      )
    """.format(
      GetDBString(sPassword),
      sUserID
    )
    dRes['token'] = "VOID"
    dRes['sample_id'] = "VOID"
    dRes['password'] = sPassword
    RunMySql(sQuery, False)
    CommitMySql()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError
  return dRes

def DBGetUnknownAccessID(sPassword):
  sRes = None
  try:
    sQuery = """
      SELECT
        id AS ID
      FROM access_control
      WHERE
        token = 'VOID'
        AND sample_id = 'VOID'
        AND UPPER(password) = {}
      """.format(
      GetDBString(sPassword.strip().upper())
    )
    dAccessControls = RunMySql(sQuery, True)
    if len(dAccessControls) > 0:
      sRes = str(dAccessControls[0]['ID'])
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError
  return sRes

def DBGetAccessFromSampleID(sSampleID):
  dRes = None
  try:
    sQuery = """
      SELECT
        id AS ID,
        sample_id AS SampleID,
        token as Token,
        password as Password,
        DATE_FORMAT("datetime", '%Y-%m-%d %H:%i:%s') AS Datetime
      FROM access_control
      WHERE
        sample_id = {}
      """.format(
      GetDBString(sSampleID.strip().upper())
    )
    dAccessControls = RunMySql(sQuery, True)
    if len(dAccessControls) > 0:
      dRes = dict()
      dRes['id'] = str(dAccessControls[0]['ID'])
      dRes['token'] = dAccessControls[0]['Token']
      dRes['sample_id'] = dAccessControls[0]['SampleID']
      dRes['password'] = dAccessControls[0]['Password']
      dRes['datetime'] = dAccessControls[0]['Datetime']
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError
  return dRes

def DBGetReportTestData(sSample):
  dRes = None
  sQuery = """
    SELECT
      A.id AS ID,
      A.accession AS Accession,
      A.name AS Name,
      DATE_FORMAT(A.birthday, '%d/%m/%Y') AS Birthday,
      TIMESTAMPDIFF(YEAR, A.birthday, A.sample_date) AS AgeYears,
      TIMESTAMPDIFF(MONTH, A.birthday, A.sample_date) AS AgeMonths,
      TIMESTAMPDIFF(DAY, A.birthday, A.sample_date) AS AgeDays,
      UPPER(A.record) AS Record,
      UPPER(A.episode) AS Episode,
      UPPER(A.state_id_1) AS StateID,
      A.department AS Department,
      UPPER(A.test_code) AS TestCode,
      UPPER(A.method_code) AS MethodCode,
      UPPER(A.sample_id) AS SampleID,
      DATE_FORMAT(A.sample_date, '%d/%m/%Y') as SampleDate,
      A.result_code AS ResultCode,
      A.result_comments AS ResultComments,
      DATE_FORMAT(A.result_datetime, '%d/%m/%Y %H:%i:%s') AS ResultDatetime,
      A.tec_validator AS TecValidator,
      DATE_FORMAT(A.validation_datetime, '%d/%m/%Y %H:%i:%s') AS ValidationDatetime,
      A.bio_validator AS BioValidator,
      A.address_1 AS Address1,
      A.address_2 AS Address2,
      A.prescriber AS Prescriber,
      A.notes AS Notes,
      B.password AS Password
    FROM tests AS A
    LEFT JOIN access_control AS B
      ON B.sample_id = A.sample_id
    WHERE
      A.test_code = 'sarscov2'
      AND A.status <> 2
      AND A.sample_id = {}
    """.format(GetDBString(sSample.strip().upper()))
  dRes = RunMySql(sQuery, True)
  dRet = None
  if len(dRes) > 0:
    dRet = dRes[0]
  return dRet

def DBGetTestFromPassword(sPassword):
  dRes = None
  sQuery = """
    SELECT
      A.id AS ID,
      A.name AS Name,
      DATE_FORMAT(A.birthday, '%Y-%m-%d') AS Birthday,
      UPPER(A.record) AS Record,
      UPPER(A.state_id_1) AS StateID,
      A.department AS Department,
      UPPER(A.test_code) AS TestCode,
      UPPER(A.sample_id) AS SampleID,
      DATE_FORMAT(A.sample_date, '%Y-%m-%d') as SampleDate,
      A.result_code AS ResultCode,
      A.result_comments AS ResultComments,
      DATE_FORMAT(A.result_datetime, '%Y-%m-%d %H:%i:%s') AS ResultDatetime,
      A.address_1 AS Address1,
      A.address_2 AS Address2,
      A.email AS Email,
      A.phone AS Phone,
      A.notes AS Notes,
      B.password AS Password
    FROM tests AS A
    LEFT JOIN access_control AS B
      ON B.sample_id = A.sample_id
    WHERE
      A.test_code = 'sarscov2'
      AND A.status <> 2
      AND B.password = {}
    """.format(GetDBString(sPassword.strip().upper()))
  dRes = RunMySql(sQuery, True)
  dRet = None
  if len(dRes) > 0:
    dRet = dRes[0]
  return dRet

def DBGetAccessFromPassword(sPassword):
  dRes = None
  try:
    sQuery = """
      SELECT
        id AS ID,
        sample_id AS SampleID,
        token as Token,
        password as Password,
        DATE_FORMAT("datetime", '%Y-%m-%d %H:%i:%s') AS Datetime
      FROM access_control
      WHERE
        password = {}
      """.format(
      GetDBString(sPassword.strip().upper())
    )
    dAccessControls = RunMySql(sQuery, True)
    if len(dAccessControls) > 0:
      dRes = dict()
      dRes['id'] = str(dAccessControls[0]['ID'])
      dRes['token'] = dAccessControls[0]['Token']
      dRes['sample_id'] = dAccessControls[0]['SampleID']
      dRes['password'] = dAccessControls[0]['Password']
      dRes['datetime'] = dAccessControls[0]['Datetime']
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError
  return dRes

def DBSetUnknownAccess(sPassword, sSampleID, sToken):
  dRes = dict()
  try:
    sQuery = """
      UPDATE access_control
      SET
        token = {},
        sample_id = {}
      WHERE
        password = {}
    """.format(
      GetDBString(sToken.strip().upper()),
      GetDBString(sSampleID.strip().upper()),
      GetDBString(sPassword.strip().upper())
    )
    dRes['token'] = sToken.strip().upper()
    dRes['sample_id'] = sSampleID.strip().upper()
    dRes['password'] = sPassword.strip().upper()
    RunMySql(sQuery, False)
    CommitMySql()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError
  return dRes

def DBDeleteAccess(sID):
  try:
    sQuery = """
      DELETE FROM access_control
      WHERE id = {}
    """.format(str(sID))
    RunMySql(sQuery, False)
    CommitMySql()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError

# -----------
# XML methods
# -----------

def XMLToDict(oXML, bRoot = True):
  if bRoot:
    return {oXML.tag : XMLToDict(oXML, False)}
  dRes = copy(oXML.attrib)
  if oXML.text:
    dRes["_text"] = oXML.text
  for oElement in oXML.findall("./*"):
    if oElement.tag not in dRes:
      dRes[oElement.tag] = []
    dRes[oElement.tag].append(XMLToDict(oElement, False))
  return dRes

# --------------
# Config methods
# --------------

def LoadConfigXML():
  global \
    sConfigFolder,\
    sConfigXML,\
    dConfig,\
    sHost,\
    sUser,\
    sPassword,\
    sDatabase,\
    sSMTPServer,\
    uSMTPPort,\
    sSMTPUser,\
    sSMTPSender,\
    sSMTPPassword,\
    uSessionTimeout,\
    uGroupListPageLen,\
    bUseAcrobatReader,\
    sAcrobatReaderPath,\
    sAcrobatReaderCommand
  oFile = open("{}\\{}".format(sConfigFolder, sConfigXML), "r", encoding = "utf-8")
  sFile = oFile.read()
  oFile.close()
  oXML = ElementTree.fromstring(sFile)
  dConfig = XMLToDict(oXML)['config']
  sHost = dConfig['host'][0]['_text']
  sUser = dConfig['user'][0]['_text']
  sPassword = dConfig['password'][0]['_text']
  sDatabase = dConfig['database'][0]['_text']
  sSMTPServer = dConfig['smtp_server'][0]['_text']
  uSMTPPort = int(dConfig['smtp_port'][0]['_text'])
  sSMTPSender = dConfig['smtp_sender'][0]['_text']
  sSMTPUser = sSMTPSender
  sSMTPPassword = dConfig['smtp_password'][0]['_text']
  uSessionTimeout = int(dConfig['transmission_session_timeout'][0]['_text'])
  uGroupListPageLen = int(dConfig['group_list_page_len'][0]['_text'])
  bUseAcrobatReader = bool(dConfig['use_acrobat_reader'][0]['_text'])
  sAcrobatReaderPath = dConfig['acrobat_reader_path'][0]['_text']
  sAcrobatReaderCommand = dConfig['acrobat_reader_command'][0]['_text']
  if bUseAcrobatReader:
    if os.path.exists("{}\\{}.exe".format(sAcrobatReaderPath, sAcrobatReaderCommand)):
      os.environ['PATH'] += os.pathsep + sAcrobatReaderPath
    else:
      bUseAcrobatReader = False
  return dConfig

def Login():
  global sUserID, oSessionDatetime, uUserProfile
  bProcess = True
  uPhase = 1
  sUserID = None
  while bProcess:
    if uPhase == 1:
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Inicio de sessao")
      print("")
      print("Use <C> para cancelar.")
      print("")
      sLoginUser = input("Utilizador: ").strip()
      if sLoginUser.lower() == "c" or sLoginUser.lower() == "s":
        bProcess = False
      elif len(sLoginUser) > 0:
        uPhase = 2
    elif uPhase == 2:
      sLoginPassword = getpass.getpass("Palavra-passe: ")
      if sLoginPassword.lower() == "c" or sLoginPassword.lower() == "s":
        bProcess = False
      elif len(sLoginPassword) > 0:
        uPhase = 3
    elif uPhase == 3:
      sQuery = """
        SELECT
          A.id AS UserID,
          A.profile AS ProfileID,
          D.code AS AppCode
        FROM users AS A
        LEFT JOIN profiles AS B
          ON B.id = A.profile AND B.status = 1
        LEFT JOIN profilesetup AS C
          ON C.profile = B.id
        LEFT JOIN apps AS D
          ON D.id = C.app
        WHERE
          A.username='{0}'
          AND A.password = md5('{1}')
          AND A.status = 1
        """.format(sLoginUser, sLoginPassword)
      ldQuery = RunMySql(sQuery, True)
      if len(ldQuery) == 0:
        print("")
        print("Atencao: Nao foi possivel iniciar sessao com as credenciais indicadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
        uPhase = 1
      else:
        bFound = False
        uUserProfile = int(ldQuery[0]['ProfileID'])
        for dRow in ldQuery:
          if dRow['AppCode'] == "transmission" or dRow['ProfileID'] == "1":
            bFound = True
            break
        if bFound:
          sUserID = str(ldQuery[0]['UserID'])
          bProcess = False
        else:
          print("Atencao: O perfil de utilizador selecionado nao inclui o acesso a esta ferramenta.")
          print("")
          uPhase = 1
  if sUserID:
    oSessionDatetime = datetime.datetime.now()
    return True
  else:
    return False

# ------------
# Email methods
# ------------

def EmailLogin(sSMTPServer, uSMTPPort, sSMTPUser, sSMTPPassword):
  global oSSLContext, oSMTPServer
  try:
    Log("")
    Log("A iniciar sessao no servidor SMTP...")
    oSSLContext = ssl.create_default_context()
    oSMTPServer = smtplib.SMTP(sSMTPServer, uSMTPPort)
    sSMTPHello1 = oSMTPServer.ehlo()
    oSMTPServer.starttls(context = oSSLContext)
    sSMTPHello2 = oSMTPServer.ehlo()
    oSMTPServer.login(sSMTPUser, sSMTPPassword)
    Log("Em sessao no servidor SMTP {}.".format(sSMTPServer))
  except Exception as dError:
    Log("Erro: Nao foi possivel iniciar sessao no servidor SMTP.")
    Log("Mensagem de erro:\n" + str(dError))
    oSMTPServer = None
    raise

def EmailLogout():
  global oSMTPServer
  try:
    oSMTPServer.quit()
    Log("")
    Log("Sessao no servidor SMTP terminada.")
    oSMTPServer = None
  except Exception as dError:
    Log("")
    Log("Erro: Nao foi possivel concluir a rotina de fim de sessao no servidor SMTP.")
    Log("Mensagem de erro:\n" + str(dError))
    oSMTPServer = None

def EmailCheckConnection():
  global oSMTPServer
  if not oSMTPServer:
    return False
  try:
    uStatus = oSMTPServer.noop()[0]
  except:
    uStatus = 0
  return True if uStatus == 250 else False 

def EmailSendWithFile(sSender, lsTo, lsCc, sSubject, sBody, sFilepath, sFileTitle):
  global oSMTPServer, sSMTPServer, uSMTPPort, sSMTPUser, sSMTPPassword
  if not EmailCheckConnection():
    try:
      EmailLogin(sSMTPServer, uSMTPPort, sSMTPUser, sSMTPPassword)
    except:
      raise
  try:
    Log("")
    Log("A enviar resultado por email...")
    oMessage = email.mime.multipart.MIMEMultipart()
    oMessage["From"] = sSender
    oMessage["To"] = ",".join(lsTo)
    oMessage["Cc"] = ",".join(lsCc)
    oMessage["Subject"] = sSubject
    oMessage.attach(email.mime.text.MIMEText(sBody, "plain"))
    with open(sFilepath, "rb") as oAttachment:
      oPart = email.mime.base.MIMEBase("application", "octet-stream")
      oPart.set_payload(oAttachment.read())
    email.encoders.encode_base64(oPart)
    oPart.add_header("Content-Disposition", f"attachment; filename= {sFileTitle}")
    oMessage.attach(oPart)
    sText = oMessage.as_string()
    oSMTPServer.sendmail(sSender, lsTo + lsCc, sText)
    Log("Email com o anexo {} ({}) para (s) seguinte(s) destinatário(s):\n  {}.".format(os.path.splitext(sFileTitle)[0], sFileTitle, ",\n  ".join(lsTo + lsCc)))
    Log("Email enviado com sucesso.")
  except Exception as dError:
    Log("Email com o anexo {} ({}) para (s) seguinte(s) destinatário(s):\n  {}.".format(os.path.splitext(sFileTitle)[0], sFileTitle, ",\n  ".join(lsTo + lsCc)))
    Log("Erro: Nao foi possivel enviar o email.")
    Log("Mensagem de erro:\n" + str(dError))
    raise

def UpdateEmails():
  global ldEmails
  sQuery = """
    SELECT
      name AS Name,
      DATE_FORMAT(birthday, '%Y-%m-%d') AS Birthday,
      UPPER(state_id) AS StateID,
      email AS Email,
      class AS Class,
      DATE_FORMAT(date, '%Y-%m-%d') AS Date
    FROM emails
    """
  ldQuery = RunMySql(sQuery, True)
  ldEmails = list()
  for dRow in ldQuery:
    dEmail = dict()
    dEmail['name'] = CleanStrName(RemoveAccents(dRow['Name']))
    dEmail['birthday'] = dRow['Birthday']
    dEmail['state_id'] = dRow['StateID'] if dRow['StateID'] and dRow['StateID'].upper() != "NULL" else ""
    dEmail['email'] = dRow['Email']
    dEmail['class'] = dRow['Class']
    dEmail['date'] = dRow['Date']
    ldEmails.append(dEmail)

def SendEmail(sSample, sEmailClass, lsTo):
  global \
    dTests,\
    sSMTPSender,\
    sReportFolder,\
    lsCc,\
    dConfig
  try:
    LISGetReport(sSample)
    sSubject = "#INSTITUTION# - Resultados de análises clínicas"
    EmailSendWithFile(
      sSMTPSender,
      lsTo,
      lsCc,
      sSubject,
      eval("\"{}\"".format(dConfig['email_results_body'][0]['_text'])).format(GetGreeting(), dTests[sSample]['name'], "#DEPARTMENT# da #INSTITUTION#"),
      "{}\\{}.pdf".format(sReportFolder, sSample),
      "{}.pdf".format(sSample))
    for sEmail in lsTo:
      DBInsertTransmission(
        sSample,
        "email",
        sEmail,
        sEmailClass,
        "void",
        "ok"
        )
  except:
    raise

def SendEmail_2(sSample, sName, sEmailClass, lsTo):
  global \
    dTests,\
    sSMTPSender,\
    sReportFolder,\
    lsCc,\
    dConfig
  try:
    sSubject = "#INSTITUTION# - Resultados de análises clínicas"
    EmailSendWithFile(
      sSMTPSender,
      lsTo,
      lsCc,
      sSubject,
      eval("\"{}\"".format(dConfig['email_results_body'][0]['_text'])).format(GetGreeting(), sName, "#DEPARTMENT# da #INSTITUTION#"),
      "{}\\{}.pdf".format(sReportFolder, sSample),
      "{}.pdf".format(sSample))
    for sEmail in lsTo:
      DBInsertTransmission(
        sSample,
        "email",
        sEmail,
        sEmailClass,
        "void",
        "ok"
        )
  except:
    raise

def DBCancelPatientTransmissionGroup(sClass, sGroup):
  global dTests
  for sSample, dTest in dTests.items():
    if "group" in dTests[sSample] and dTests[sSample]['group']['title'] == sGroup and dTests[sSample]['group']['class'] == sClass:
      print("")
      print("Cancelar transmissao ao utente: Amostra {}, tipo \"{}\" e grupo \"{}\".".format(sSample, sClass, sGroup))
      try:
        DBInsertTransmission(
          sSample,
          "unknown",
          "void",
          "patient",
          "void",
          "canceled"
          )
      except:
        print("Erro: Nao foi possivel introduzir a transmissao. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
        raise

# -------------
# Tests methods
# -------------

def UpdateTests(sStartDatetime, sEndDatetime, sName, sStateID, sRecord, sSample, sPassword, bReset = False):
  global dTests, sStandardDateFormat, sStandardDatetimeFormat, uMaxEmailDaysDiff
  lsVoid = ["null", "NULL", "void", "VOID", "", None]
  sQuery = """
    SELECT
      A.id AS ID,
      A.name AS Name,
      DATE_FORMAT(A.birthday, '%Y-%m-%d') AS Birthday,
      UPPER(A.record) AS Record,
      UPPER(A.state_id_1) AS StateID,
      A.department AS Department,
      UPPER(A.test_code) AS TestCode,
      UPPER(A.sample_id) AS SampleID,
      UPPER(A.accession) AS Accession,
      DATE_FORMAT(A.sample_date, '%Y-%m-%d') as SampleDate,
      A.result_code AS ResultCode,
      A.result_comments AS ResultComments,
      DATE_FORMAT(A.result_datetime, '%Y-%m-%d %H:%i:%s') AS ResultDatetime,
      A.address_1 AS Address1,
      A.address_2 AS Address2,
      A.email AS Email,
      A.phone AS Phone,
      A.notes AS Notes,
      B.id AS TransmissionID,
      B.class AS TransmissionClass,
      B.description AS TransmissionDescription,
      B.recipient_class AS TransmissionRecipientClass,
      B.recipient_description AS TransmissionRecipientDescription,
      DATE_FORMAT(B."datetime", '%Y-%m-%d %H:%i:%s') AS TransmissionDatetime,
      B.status AS TransmissionStatus,
      C.id AS EmailID,
      C.email AS EmailAddress,
      C.class AS EmailClass,
      DATE_FORMAT(C.date, '%Y-%m-%d') as EmailDate,
      D.id AS GroupID,
      D."group" AS GroupTitle,
      D.department AS GroupDepartment,
      D.category AS GroupCategory,
      D.location AS GroupLocation,
      D.class AS GroupClass,
      DATE_FORMAT(D.date, '%Y-%m-%d') as GroupDate,
      E.password AS Password
    FROM tests AS A
    LEFT JOIN transmissions AS B
      ON B.sample_id = A.sample_id
    LEFT JOIN emails AS C
      ON
      (
        C.state_id = A.state_id_1
        OR
        (
          (
            C.name LIKE CONCAT('%', REPLACE(A.name, ' ', '%'), '%')
            OR
            A.name LIKE CONCAT('%', REPLACE(C.name, ' ', '%'), '%')
          )
          AND
          C.birthday = A.birthday
        )
      )
      AND
      ABS(DATEDIFF(C.date, A.sample_date)) <= {}
    LEFT JOIN groups AS D
      ON
        D.status = 1
        AND
        (
          D.sample = A.sample_id
          OR
          D.state_id = A.state_id_1
          OR
          (
            (
              D.name LIKE CONCAT('%', REPLACE(A.name, ' ', '%'), '%')
              OR
              A.name LIKE CONCAT('%', REPLACE(D.name, ' ', '%'), '%')
            )
            AND
            D.birthday = A.birthday
          )
        )
    LEFT JOIN access_control AS E
      ON E.sample_id = A.sample_id
    WHERE
      A.test_code = 'sarscov2'
      AND A.status <> 2
    """.format(uMaxEmailDaysDiff)
  if sStartDatetime and sStartDatetime != "":
    sQuery = sQuery + " AND A.result_datetime >= '{}'".format(sStartDatetime) 
  if sEndDatetime and sEndDatetime != "":
    sQuery = sQuery + " AND A.result_datetime <= '{}'".format(sEndDatetime)
  if sName and sName != "":
    sNamePattern = "%"+"%".join(RemoveAccents(sName).split())+"%"
    sQuery = sQuery + " AND A.name LIKE '{}'".format(sNamePattern)
  if sStateID and sStateID != "":
    sQuery = sQuery + " AND UPPER(A.state_id_1) = '{}'".format(sStateID.strip().upper())
  if sRecord and sRecord != "":
    sQuery = sQuery + " AND UPPER(A.record) = '{}'".format(sRecord.strip().upper())
  if sSample and sSample != "":
    sQuery = sQuery + " AND (UPPER(A.sample_id) = '{0}' OR UPPER(A.accession) = '{0}')".format(sSample.strip().upper())
  if sPassword and sPassword != "":
    sQuery = sQuery + " AND UPPER(E.password) = '{}'".format(sPassword.strip().upper())
  ldQuery = RunMySql(sQuery, True)
  if bReset:
    dTests = dict()
  for dRow in ldQuery:
    sCurrSample = dRow['SampleID']
    if sCurrSample in dTests:
      dTests[sCurrSample] = dict()
      dTests[sCurrSample]['transmissions'] = list()
      dTests[sCurrSample]['transmission_ids'] = list()
      dTests[sCurrSample]['groups'] = list()
      dTests[sCurrSample]['group_ids'] = list()
      dTests[sCurrSample]['emails'] = list()
      dTests[sCurrSample]['email_ids'] = list()
  for dRow in ldQuery:
    sCurrSample = dRow['SampleID']
    if sCurrSample not in dTests:
      dTests[sCurrSample] = dict()
      dTests[sCurrSample]['transmissions'] = list()
      dTests[sCurrSample]['transmission_ids'] = list()
      dTests[sCurrSample]['groups'] = list()
      dTests[sCurrSample]['group_ids'] = list()
      dTests[sCurrSample]['emails'] = list()
      dTests[sCurrSample]['email_ids'] = list()
    dTests[sCurrSample]['id'] = str(dRow['ID'])
    dTests[sCurrSample]['name'] = dRow['Name']
    dTests[sCurrSample]['birthday'] = dRow['Birthday']
    dTests[sCurrSample]['accession'] = dRow['Accession'] if dRow['Accession'] and dRow['Accession'].upper() != "NULL" else ""
    dTests[sCurrSample]['record'] = dRow['Record'] if dRow['Record'] and dRow['Record'].upper() != "NULL" else ""
    dTests[sCurrSample]['state_id'] = dRow['StateID'] if dRow['StateID'] and dRow['StateID'].upper() != "NULL" else ""
    dTests[sCurrSample]['department'] = dRow['Department'] if dRow['Department'] and dRow['Department'].upper() != "NULL" else ""
    dTests[sCurrSample]['test_code'] = dRow['TestCode']
    dTests[sCurrSample]['sample_date'] = dRow['SampleDate']
    dTests[sCurrSample]['result'] = dRow['ResultCode'] if dRow['ResultCode'] and dRow['ResultCode'].upper() != "NULL" else ""
    dTests[sCurrSample]['result_comments'] = dRow['ResultComments'] if dRow['ResultComments'] and dRow['ResultComments'].upper() != "NULL" else ""
    dTests[sCurrSample]['result_datetime'] = dRow['ResultDatetime'] if dRow['ResultDatetime'] and dRow['ResultDatetime'].upper() != "NULL" else ""
    dTests[sCurrSample]['address_1'] = dRow['Address1'] if dRow['Address1'] and dRow['Address1'].upper() != "NULL" else ""
    dTests[sCurrSample]['address_2'] = dRow['Address2'] if dRow['Address2'] and dRow['Address2'].upper() != "NULL" else ""
    dTests[sCurrSample]['email'] = dRow['Email'] if dRow['Email'] and dRow['Email'].upper() != "NULL" else ""
    dTests[sCurrSample]['notes'] = dRow['Notes'] if dRow['Notes'] and dRow['Notes'].upper() != "NULL" else ""
    if dRow['Password'] and dRow['Password'].upper() != "NULL" and dRow['Password'] != "":
      dTests[sCurrSample]['password'] = dRow['Password']
    if dRow['TransmissionClass'] and dRow['TransmissionClass'].upper() != "NULL" and dRow['TransmissionClass'] != "":
      if dRow['TransmissionID'] not in dTests[sCurrSample]['transmission_ids']:
        dTests[sCurrSample]['transmissions'].append({
          'id': dRow['TransmissionID'],
          'type': "transmission",
          'class': dRow['TransmissionClass'],
          'description': dRow['TransmissionDescription'],
          'recipient_class': dRow['TransmissionRecipientClass'],
          'recipient_description': dRow['TransmissionRecipientDescription'],
          'datetime': dRow['TransmissionDatetime'],
          'status': dRow['TransmissionStatus']
        })
        dTests[sCurrSample]['transmission_ids'].append(dRow['TransmissionID'])
    if dRow['EmailID'] and dRow['EmailID'].upper() != "NULL" and dRow['EmailID'] != "":
      if dRow['EmailID'] not in dTests[sCurrSample]['email_ids']:
        dTests[sCurrSample]['emails'].append({
          'id': dRow['EmailID'],
          'address': dRow['EmailAddress'],
          'class': dRow['EmailClass'],
          'date': dRow['EmailDate']
        })
        dTests[sCurrSample]['email_ids'].append(dRow['EmailID'])
    if dRow['GroupID'] and dRow['GroupID'].upper() != "NULL" and dRow['GroupID'] != "":
      if dRow['GroupID'] not in dTests[sCurrSample]['group_ids']:
        dTests[sCurrSample]['groups'].append({
          'id': dRow['GroupID'],
          'title': dRow['GroupTitle'],
          'class': dRow['GroupClass'],
          'department': dRow['GroupDepartment'],
          'category': dRow['GroupCategory'],
          'location': dRow['GroupLocation'],
          'date': dRow['GroupDate']
        })
        dTests[sCurrSample]['group_ids'].append(int(dRow['GroupID']))
      if dRow['GroupClass'] == "internal":
        dTests[sCurrSample]['internal'] = {
          'id': dRow['GroupID'],
          'title': dRow['GroupTitle'],
          'class': dRow['GroupClass'],
          'department': dRow['GroupDepartment'],
          'category': dRow['GroupCategory'],
          'location': dRow['GroupLocation'],
          'date': dRow['GroupDate']
        }
  for sCurrSample, dTest in dTests.items():
    for dEmail in dTests[sCurrSample]['emails']:
      dEmail['sent'] = False
      for dTransmission in dTests[sCurrSample]['transmissions']:
        if dTransmission['class'] == "email" and dTransmission['description'] == dEmail['address']:
          dEmail['sent'] = True
    for dEmail in dTests[sCurrSample]['emails']:
      if not dEmail['sent']:
        dTests[sCurrSample]['transmissions'].append({
          'id': dEmail['id'],
          'type': "email",
          'class': "email",
          'description': dEmail['address'],
          'recipient_class': dEmail['class'],
          'recipient_description': "void",
          'datetime': ConvertDatetime(dEmail['date'], sStandardDateFormat, sStandardDatetimeFormat),
          'status': "pending"
        })
    if 'internal' in dTest:
      bInternalSent = False
      for dTransmission in dTests[sCurrSample]['transmissions']:
        if dTransmission['class'] == "internal":
          bInternalSent = True
          break
      if not bInternalSent:
        dTests[sCurrSample]['transmissions'].append({
          'id': dTest['internal']['id'],
          'type': "internal",
          'class': "internal",
          'description': "void",
          'recipient_class': "clinician",
          'recipient_description': dTest['internal']['title'] if dTest['internal']['title'] not in lsVoid else dTest['internal']['department'],
          'datetime': ConvertDatetime(dTest['sample_date'], sStandardDateFormat, sStandardDatetimeFormat),
          'status': "pending"
        })
  for sCurrSample, dTest in dTests.items():
    if len(dTest['groups']) > 0:
      uIndex = dTest['group_ids'].index(max(dTest['group_ids']))
      dTest['group'] = {
        'id': dTest['groups'][uIndex]['id'],
        'title': dTest['groups'][uIndex]['title'],
        'class': dTest['groups'][uIndex]['class'],
        'department': dTest['groups'][uIndex]['department'],
        'category': dTest['groups'][uIndex]['category'],
        'location': dTest['groups'][uIndex]['location'],
        'date': dTest['groups'][uIndex]['date']}

def GetGroups(sStartDatetime, sEndDatetime, sTitle):
  dRes = dict()
  lsVoid = ["null", "NULL", "void", "VOID", "", None]
  sQuery = """
   SELECT
     B.id AS ID,
     B."group" AS Title,
     B.institution_class AS Class,
     B.class AS PatientClass,
     A.sample_id AS Sample,
     A.accession AS Accession,
     A.name AS Name,
     A.birthday AS Birthday,
     A.state_id_1 AS StateID1,
     A.record AS Record,
     A.episode AS Episode,
     B.department AS Department,
     B.location AS Location,
     B.category AS Category
    FROM tests AS A
    LEFT JOIN groups AS B
      ON
        B.status = 1
        AND
        (
          B.sample = A.sample_id
          OR
          B.state_id = A.state_id_1
          OR
          (
            (
              B.name LIKE CONCAT('%', REPLACE(A.name, ' ', '%'), '%')
              OR
              A.name LIKE CONCAT('%', REPLACE(B.name, ' ', '%'), '%')
            )
            AND
            B.birthday = A.birthday
          )
        )
    WHERE
      A.test_code = 'sarscov2'
      AND A.status <> 2
      AND B.id IS NOT NULL
      AND
      (
        (
          result_code <> 'waiting'
          AND
          A.result_datetime >= '{}'
          AND
          A.result_datetime <= '{}'
        )
        OR
        (
          result_code = 'waiting'
          AND
          TIMESTAMPDIFF(DAY, sample_date, NOW()) <= 2
        )
      )
    ORDER BY B."group" ASC
    """.format(sStartDatetime, sEndDatetime)
  if sTitle:
    sQuery = sQuery + " AND B.\"group\" LIKE '%{}%'".format("%".join(sTitle.split()))
  ldQuery = RunMySql(sQuery, True)
  for dRow in ldQuery:
    tKey = (dRow['Title'], dRow['Class'])
    if tKey not in dRes:
      dRes[tKey] = (list(), dict(), dict())
    dRes[tKey][0].append(dRow['ID'])
    if dRow['PatientClass'] not in dRes[tKey][1]:
      dRes[tKey][1][dRow['PatientClass']] = list()
    dRes[tKey][1][dRow['PatientClass']].append(dRow['ID'])
    if dRow['Sample']:
      dNewSample = dict()
      dNewSample['id'] = dRow['Sample']
      dNewSample['group_id'] = dRow['ID']
      dNewSample['accession'] = dRow['Accession']
      dNewSample['name'] = dRow['Name']
      dNewSample['birthday'] = dRow['Birthday']
      dNewSample['state_id_1'] = dRow['StateID1']
      dNewSample['record'] = dRow['Record']
      dNewSample['episode'] = dRow['Episode']
      dNewSample['patient_class'] = dRow['PatientClass']
      dNewSample['department'] = dRow['Department']
      dNewSample['location'] = dRow['Location']
      dNewSample['category'] = dRow['Category']
      dRes[tKey][2][dRow['Sample']] = dNewSample
  return dRes

def UpdateGroups(tGroup, lsIDs, sTitle, sClass, bAllTime):
  global sUserID
  if not bAllTime:
    for sID in lsIDs:
      sQuery = """
        UPDATE groups
        SET
          "group" = '{}',
          institution_class = '{}',
          source = 'transmission',
          "datetime" = NOW(),
          user = {}
        WHERE
          id = {}
        """.format(sTitle, sClass, sUserID, sID)
      RunMySql(sQuery, False)
    CommitMySql()
  else:
    sQuery = """
      UPDATE groups
      SET
        "group" = '{}',
        institution_class = '{}',
        source = 'transmission',
        "datetime" = NOW(),
        user = {}
      WHERE
        "group" = '{}'
        AND institution_class = '{}'
      """.format(sTitle, sClass, sUserID, tGroup[0], tGroup[1])
    RunMySql(sQuery, False)
    CommitMySql()
  return

def DeleteGroups(tGroup, lsIDs, bAllTime):
  global sUserID
  if not bAllTime:
    for sID in lsIDs:
      sQuery = "DELETE FROM groups WHERE id = {}".format(sID)
      sQuery = """
        UPDATE GROUPS
        SET
          status = 0,
          source = 'transmission',
          "datetime" = NOW(),
          user = {}
        WHERE id = {}
      """.format(sUserID, sID)
      RunMySql(sQuery, False)
    CommitMySql()
  else:
    sQuery = """
      DELETE FROM groups
      WHERE
        "group" = '{}'
        AND institution_class = '{}'
      """.format(tGroup[0], tGroup[1])
    sQuery = """
      UPDATE groups
      SET
        status = 0,
        source = 'transmission',
        "datetime" = NOW(),
        user = {}
      WHERE
        "group" = '{}'
        AND institution_class = '{}'
      """.format(sUserID, tGroup[0], tGroup[1])
    RunMySql(sQuery, False)
    CommitMySql()
  return

def InputNewAccessControl():
  tRes = None
  sREToken = r"^[A-Z]\d{5}$"
  sRESampleID = r"^\d{6,7}$"
  uPhase = 0
  bInput = True
  while bInput:
    if uPhase == 0:
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Nova senha de acesso")
      print("")
      print("Use <C> para cancelar.")
      uPhase = 1
    if uPhase == 1:
      print("")
      sInput = input("Numero unico de ficha (NC): ").strip().lower()[0:7]
      if sInput == "c":
        bInput = False
      elif sInput != "" and re.match(sRESampleID, sInput.upper()):
        uPhase = 2
      else:
        print("Atencao: Indique um numero de ficha (NC) valido.")
        uPhase = 1
    if uPhase == 2:
      sSampleID = sInput.upper()
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Nova senha de acesso")
      print("")
      print("Use <C> para cancelar.")
      print("")
      print("Numero unico de ficha (NC): {}".format(sSampleID))
      uPhase = 3
    if uPhase == 3:
      print("")
      sInput = input("Numero de amostra (NT): ").strip().lower()[0:6]
      if sInput == "c":
        uPhase = 0
      elif sInput != "" and re.match(sREToken, sInput.upper()):
        uPhase = 4
      else:
        print("Atencao: Indique um numero de amostra (NT) valido.")
        uPhase = 3
    if uPhase == 4:
      sToken = sInput.upper()
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Nova senha de acesso")
      print("")
      print("Use <C> para cancelar.")
      print("")
      print("Numero unico de ficha (NC): {}".format(sSampleID))
      print("")
      print("Numero de amostra (NT): {}".format(sToken))
      print("")
      tRes = (sSampleID, sToken)
      bInput = False
  return tRes

def InputActivateAccessControl():
  tRes = None
  sREToken = r"^[A-Z]\d{5}$"
  sRESampleID = r"^\d{6,7}$"
  sREAccessCode = r"^([A-Z]|[0-9]){8}$"
  uPhase = 0
  bInput = True
  while bInput:
    if uPhase == 0:
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Associar senha de acesso")
      print("")
      print("Use <C> para cancelar.")
      uPhase = 1
    if uPhase == 1:
      print("")
      sInput = input("Senha de acesso: ").strip().lower()
      if sInput == "c":
        bInput = False
      elif sInput != "" and re.match(sREAccessCode, sInput.upper()):
        uPhase = 2
      else:
        print("Atencao: Indique uma senha de acesso valida.")
        uPhase = 1
    if uPhase == 2:
      sAccessCode = sInput.upper()
      if not DBGetUnknownAccessID(sAccessCode):
        print("Atencao: Senha de acesso invalida ou nao encontrada.")
        uPhase = 1
      else:
        uPhase = 3
    if uPhase == 3:
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Nova senha de acesso")
      print("")
      print("Use <C> para cancelar.")
      print("")
      print("Senha de acesso: {}".format(sAccessCode))
      uPhase = 4
    if uPhase == 4:
      print("")
      sInput = input("Numero unico de ficha (NC): ").strip().lower()[0:7]
      if sInput == "c":
        bInput = False
      elif sInput != "" and re.match(sRESampleID, sInput.upper()):
        uPhase = 5
      else:
        print("Atencao: Indique um numero de ficha (NC) valido.")
        uPhase = 4
    if uPhase == 5:
      sSampleID = sInput.upper()
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Nova senha de acesso")
      print("")
      print("Use <C> para cancelar.")
      print("")
      print("Senha de acesso: {}".format(sAccessCode))
      print("")
      print("Numero unico de ficha (NC): {}".format(sSampleID))
      uPhase = 6
    if uPhase == 6:
      print("")
      sInput = input("Numero de amostra (NT): ").strip().lower()[0:6]
      if sInput == "c":
        uPhase = 0
      elif sInput != "" and re.match(sREToken, sInput.upper()):
        uPhase = 7
      else:
        print("Atencao: Indique um numero de amostra (NT) valido.")
        uPhase = 6
    if uPhase == 7:
      sToken = sInput.upper()
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Nova senha de acesso")
      print("")
      print("Use <C> para cancelar.")
      print("")
      print("Senha de acesso: {}".format(sAccessCode))
      print("")
      print("Numero unico de ficha (NC): {}".format(sSampleID))
      print("")
      print("Numero de amostra (NT): {}".format(sToken))
      print("")
      sInput = input("Confirmar estes dados (s/n)? ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "s":
        uPhase = 8
      elif sInput == "n":
        uPhase = 0
      else:
        uPhase = 7
    if uPhase == 8:
      dAccess = DBGetAccessFromSampleID(sSampleID)
      if dAccess:
        uPhase = 9
      else:
        uPhase = 10
    if uPhase == 9:
      print("")
      print("Ja existe o seguinte registo de senha de acesso:\n- Codigo: {}\n- NC: {}\n- NT: {}".format(dAccess['password'], dAccess['sample_id'], dAccess['token']))
      print("")
      sInput = input("Deseja eliminar esta senha de acesso (s/n)? ").strip().lower()
      if sInput == "c":
        bInput = False
      elif sInput == "s":
        DBDeleteAccess(dAccess['id'])
        uPhase = 10
      elif sInput == "n":
        uPhase = 0
      else:
        uPhase = 9
    if uPhase == 10:
      tRes = (sAccessCode, sSampleID, sToken)
      bInput = False
  return tRes

def InputSelectSearchMode():
  global sDateFormat, sStandardDateFormat, sStandardDatetimeFormat
  global oSessionDatetime, uSessionTimeout
  global uUserProfile
  sREAccessCode = r"^([A-Z]|[0-9]){8}$"
  sEmailRE = r"^[A-Za-z0-9\.\+_-]+@[A-Za-z0-9\._-]+\.[a-zA-Z]*$"
  tRes = None
  bInput = True
  uPhase = 0
  while bInput:
    if uPhase == 0:
      if (datetime.datetime.now() - oSessionDatetime).seconds > uSessionTimeout:
        return ("session_timeout")
      oSessionDatetime = datetime.datetime.now()
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Transmissao pendente;")
      print("- 2: Resultado nao transmitido e sem senha de acesso;")
      print("- 3: Data de resultado;")
      print("- 4: Nome;")
      print("- 5: #STATEID1#;")
      print("- 6: #INTID1#;")
      print("- 7: Amostra;")
      print("- 8: Senha de acesso;")
      print("- 9: Resultado nao transmitido;")
      print("- 10: Nova senha de acesso;")
      print("- 11: Nova senha de acesso sem amostra;")
      print("- 12: Associar senha de acesso;")
      print("- 13: Eliminar senha de acesso;")
      print("- 14: Impressao interna pendente;")
      print("- 15: Enviar email com senha de acesso;")
      print("- 16: Imprimir instrucoes de acesso ao resultado sem senha;")
      print("- 17: Gerir grupos;")
      print("- 18: Modo de intrucoes de acesso em fila;")
      if uUserProfile == 1:
        print("- 19: Imprimir intrucoes de acesso atraves do NT;")
        print("- X1: Enviar email com ficheiro selecionado.")
      else:
        print("- 19: Imprimir intrucoes de acesso atraves do NT.")
      print("")
      print("Use <C> para cancelar.")
      print("")
      sInput = input("Modo de procura: ").strip().lower()
      if (datetime.datetime.now() - oSessionDatetime).seconds > uSessionTimeout:
        return ("session_timeout")
      oSessionDatetime = datetime.datetime.now()
      if sInput == "1":
        uPhase = 60
      if sInput == "2":
        uPhase = 70
      if sInput == "3":
        uPhase = 10
      if sInput == "4":
        uPhase = 20
      if sInput == "5":
        uPhase = 30
      if sInput == "6":
        uPhase = 40
      if sInput == "7":
        uPhase = 50
      if sInput == "10":
        uPhase = 80
      if sInput == "8":
        uPhase = 90
      if sInput == "11":
        uPhase = 100
      if sInput == "12":
        uPhase = 110
      if sInput == "9":
        uPhase = 120
      if sInput == "13":
        uPhase = 130
      if sInput == "14":
        uPhase = 140
      if sInput == "15":
        uPhase = 150
      if sInput == "16":
        uPhase = 160
      if sInput == "17":
        uPhase = 170
      if sInput == "18":
        uPhase = 180
      if sInput == "19":
        uPhase = 190
      if sInput == "x1" and uUserProfile == 1:
        uPhase = 1010
      if sInput == "c":
        bInput = False
    if uPhase == 10:
      print("")
      sInput = input("Data inicial (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeStart = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 11
    if uPhase == 11:
      print("")
      sInput = input("Data final (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeEnd = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 12
    if uPhase == 12:
      if (oDatetimeEnd - oDatetimeStart).days < 0:
        print("Atencao: A data inicial tem que ser anterior a data final.")
        uPhase = 10
      else:
        sDatetimeStart = oDatetimeStart.replace(hour = 0, minute = 0, second = 0).strftime(sStandardDatetimeFormat)
        sDatetimeEnd = oDatetimeEnd.replace(hour = 23, minute = 59, second = 59).strftime(sStandardDatetimeFormat)
        tRes = ("date", (sDatetimeStart, sDatetimeEnd))
        bInput = False
    if uPhase == 20:
      print("")
      sInput = input("Nome: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        tRes = ("name", sInput)
        bInput = False
    if uPhase == 30:
      print("")
      sInput = input("Numero #STATE_ID_1#: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        tRes = ("state_id", sInput.upper())
        bInput = False
    if uPhase == 40:
      print("")
      sInput = input("#INTID1#: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        tRes = ("record", sInput.upper())
        bInput = False
    if uPhase == 50:
      print("")
      sInput = input("Amostra: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        tRes = ("sample_id", sInput.upper())
        bInput = False
    if uPhase == 60:
      print("")
      sInput = input("Data inicial (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeStart = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 61
    if uPhase == 61:
      print("")
      sInput = input("Data final (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeEnd = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 62
    if uPhase == 62:
      if (oDatetimeEnd - oDatetimeStart).days < 0:
        print("Atencao: A data inicial tem que ser anterior a data final.")
        uPhase = 60
      else:
        sDatetimeStart = oDatetimeStart.replace(hour = 0, minute = 0, second = 0).strftime(sStandardDatetimeFormat)
        sDatetimeEnd = oDatetimeEnd.replace(hour = 23, minute = 59, second = 59).strftime(sStandardDatetimeFormat)
        tRes = ("unsent", (sDatetimeStart, sDatetimeEnd))
        bInput = False
    if uPhase == 70:
      print("")
      sInput = input("Data inicial (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeStart = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 71
    if uPhase == 71:
      print("")
      sInput = input("Data final (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeEnd = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 72
    if uPhase == 72:
      if (oDatetimeEnd - oDatetimeStart).days < 0:
        print("Atencao: A data inicial tem que ser anterior a data final.")
        uPhase = 70
      else:
        sDatetimeStart = oDatetimeStart.replace(hour = 0, minute = 0, second = 0).strftime(sStandardDatetimeFormat)
        sDatetimeEnd = oDatetimeEnd.replace(hour = 23, minute = 59, second = 59).strftime(sStandardDatetimeFormat)
        tRes = ("no_transmission", (sDatetimeStart, sDatetimeEnd))
        bInput = False
    if uPhase == 80:
      tAccess = InputNewAccessControl()
      if tAccess:
        dAccess = DBInsertAccessControl(tAccess[0], tAccess[1])
        CreateAccessInfo(dAccess['token'], dAccess['password'])
      else:
        uPhase = 0
    if uPhase == 90:
      print("")
      sInput = input("Senha de acesso: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput != "" and re.match(sREAccessCode, sInput.upper()):
        uPhase = 91
      else:
        print("Atencao: Indique uma senha de acesso valida.")
    if uPhase == 91:
      tRes = ("access", sInput.upper())
      bInput = False
    if uPhase == 100:
      tRes = ("access_unknown", None)
      bInput = False
    if uPhase == 110:
      tAccess = InputActivateAccessControl()
      if tAccess:
        DBSetUnknownAccess(tAccess[0], tAccess[1], tAccess[2])
        CreateAccessInfo(tAccess[2], tAccess[0])
      else:
        uPhase = 0
    if uPhase == 120:
      print("")
      sInput = input("Data inicial (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeStart = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 121
    if uPhase == 121:
      print("")
      sInput = input("Data final (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeEnd = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 122
    if uPhase == 122:
      if (oDatetimeEnd - oDatetimeStart).days < 0:
        print("Atencao: A data inicial tem que ser anterior a data final.")
        uPhase = 120
      else:
        sDatetimeStart = oDatetimeStart.replace(hour = 0, minute = 0, second = 0).strftime(sStandardDatetimeFormat)
        sDatetimeEnd = oDatetimeEnd.replace(hour = 23, minute = 59, second = 59).strftime(sStandardDatetimeFormat)
        tRes = ("no_transmission_2", (sDatetimeStart, sDatetimeEnd))
        bInput = False
    if uPhase == 130:
      print("")
      sInput = input("Senha de acesso: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput != "" and re.match(sREAccessCode, sInput.upper()):
        uPhase = 131
      else:
        print("Atencao: Indique uma senha de acesso valida.")
    if uPhase == 131:
      sPassword = sInput.upper()
      dAccess = DBGetAccessFromPassword(sPassword)
      if dAccess:
        uPhase = 132
      else:
        print("Atencao: Senha de acesso nao encontrada.")
        uPhase = 130
    if uPhase == 132:
      print("")
      print("Registo de senha de acesso:\n- NT: {}\n- NC: {}\n- Codigo: {}".format(
        dAccess['token'] if dAccess['token'].upper() != "VOID" else "(nao usado)",
        dAccess['sample_id'] if dAccess['sample_id'].upper() != "VOID" else "(nao usado)",
        dAccess['password']) if dAccess['password'].upper() != "VOID" else "(nao usado)")
      print("")
      sInput = input("Deseja eliminar esta senha de acesso (s/n)? ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "s":
        tRes = ("access_delete", dAccess['id'])
        bInput = False
      elif sInput == "n":
        uPhase = 0
      else:
        uPhase = 132
    if uPhase == 140:
      print("")
      sInput = input("Data inicial (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeStart = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 141
    if uPhase == 141:
      print("")
      sInput = input("Data final (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeEnd = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 142
    if uPhase == 142:
      if (oDatetimeEnd - oDatetimeStart).days < 0:
        print("Atencao: A data inicial tem que ser anterior a data final.")
        uPhase = 140
      else:
        sDatetimeStart = oDatetimeStart.replace(hour = 0, minute = 0, second = 0).strftime(sStandardDatetimeFormat)
        sDatetimeEnd = oDatetimeEnd.replace(hour = 23, minute = 59, second = 59).strftime(sStandardDatetimeFormat)
        tRes = ("unsent_internal", (sDatetimeStart, sDatetimeEnd))
        bInput = False
    if uPhase == 150:
      print("")
      sInput = input("Senha de acesso: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput != "" and re.match(sREAccessCode, sInput.upper()):
        sAccessCode = sInput
        uPhase = 151
      else:
        print("Atencao: Indique uma senha de acesso valida.")
    if uPhase == 151:
      dTest = DBGetTestFromPassword(sAccessCode)
      if not dTest:
        print("Atencao: Senha de acesso nao encontrada ou nao associada.")
        uPhase = 150
      elif dTest['ResultCode'].lower() == "waiting":
        print("Informacaoo: Resultado pendente.")
        uPhase = 150
      else:
        uPhase = 152
    if uPhase == 152:
      print("")
      sInput = input("Email: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput != "":
        sEmail = RemoveAccents(sInput)
        if re.match(sEmailRE, sEmail):
          uPhase = 153
        else:
          print("Atencao: Indique um endereco de email valido.")
          uPhase = 152
      else:
        uPhase = 152
    if uPhase == 153:
      print("")
      print("Indique o tipo de email:")
      print("- 1: Email do proprio;")
      print("- 2: Email de clinico;")
      print("- 3: Email institucional;")
      print("- 4: Email de cuidador;")
      print("- 5: Email de elemento de familia.")
      print("")
      sInput = input("Tipo de email: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "patient",
          "2": "clinician",
          "3": "manager",
          "4": "caregiver",
          "5": "family"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique um tipo de email valido.")
        else:
          sEmailClass = dClassTrans[sInput]
          uPhase = 154
    if uPhase == 154:
      tRes = ("email_access", sAccessCode, sEmail, sEmailClass)
      bInput = False
    if uPhase == 160:
      try:
        CreateAccessInfo_NoToken()
      except:
        pass
      uPhase = 0
    if uPhase == 170:
      print("")
      sInput = input("Data inicial (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeStart = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 171
    if uPhase == 171:
      print("")
      sInput = input("Data final (dd/mm/aaaa): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        try:
          oDatetimeEnd = datetime.datetime.strptime(sInput, sDateFormat)
        except:
          print("Atencao: Indique uma data valida no formato dd/mm/aaaa.")
        else:
          uPhase = 172
    if uPhase == 172:
      if (oDatetimeEnd - oDatetimeStart).days < 0:
        print("Atencao: A data inicial tem que ser anterior a data final.")
        uPhase = 170
      else:
        uPhase = 173
    if uPhase == 173:
      print("")
      sInput = input("Titulo do grupo a procurar (vazio para selecionar todos):\n  ").strip().lower()
      if sInput == "c":
        uPhase = 0
      else:
        sTitle = sInput
        uPhase = 174
    if uPhase == 174:
      sDatetimeStart = oDatetimeStart.replace(hour = 0, minute = 0, second = 0).strftime(sStandardDatetimeFormat)
      sDatetimeEnd = oDatetimeEnd.replace(hour = 23, minute = 59, second = 59).strftime(sStandardDatetimeFormat)
      tRes = ("groups", (sDatetimeStart, sDatetimeEnd, sTitle))
      bInput = False
    if uPhase == 180:
      bInput = True
      bCancel2 = False
      while bInput:
        sInput = input("\nCertifique-se de que existe um ficheiro 'fichas.txt'\natualizado na pasta de carregamento.\n\nDeseja continuar (s/n)? ").strip().lower()
        if sInput == "s":
          bInput = False
        elif sInput == "n" or sInput == "c":
          bCancel2 = True
          bInput = False
      if bCancel2:
        uPhase = 0
        bInput = True
        continue
      print("\nA carregar ficheiro 'fichas.txt'...")
      LoadOrders()
      for sSample, dOrder in dOrders.items():
        if DBCheckAccessControl(sSample, dOrder['accession']):
          continue
        bInput = True
        while bInput:
          sInput = input("\nFolha de acesso para o utente com seguintes dados:\n- Nome: {}\n- Data de nascimento: {}\n- Numero de ficha: {}\n- Numero de amostra: {}\n- Data e hora de ativacao: {}\n\nGerar (s/n/c)? ".format(dOrder['name'], dOrder['birthday'], sSample, dOrder['accession'], dOrder['activation_datetime'])).strip().lower()
          if sInput == "s":
            dAccess = DBInsertAccessControl(sSample, dOrder['accession'])
            CreateAccessInfo(dAccess['token'], dAccess['password'])
            bInput = False
          elif sInput == "c":
            bCancel2 = True
            bInput = False
          elif sInput == "n":
            bInput = False
        if bCancel2:
          break
      uPhase = 0
      bInput = True
      continue
    if uPhase == 190:
      bInput = True
      bCancel2 = False
      while bInput:
        sInput = input("\nCertifique-se de que existe um ficheiro 'fichas.txt'\natualizado na pasta de carregamento.\n\nDeseja continuar (s/n)? ").strip().lower()
        if sInput == "s":
          bInput = False
        elif sInput == "n" or sInput == "c":
          bCancel2 = True
          bInput = False
      if bCancel2:
        uPhase = 0
        bInput = True
        continue
      print("\nA carregar ficheiro 'fichas.txt'...")
      LoadOrders()
      bInput = True
      while bInput:
        sInput = input("\nAmostra (c para cancelar): ").strip().upper()[0:6]
        if sInput == "C":
          bInput = False
        elif sInput:
          sFoundSample = None
          for sSample, dOrder in dOrders.items():
            if dOrder['accession'].upper() == sInput:
              sFoundSample = sSample
              break
          if not sFoundSample:
            print("\nAtencao: Amostra nao encontrada.")
          else:
            print("\nFolha de acesso para o utente com seguintes dados:\n- Nome: {}\n- Data de nascimento: {}\n- Numero de ficha: {}\n- Numero de amostra: {}\n- Data e hora de ativacao: {}".format(dOrder['name'], dOrder['birthday'], sSample, dOrder['accession'], dOrder['activation_datetime']))
            dAccess = DBInsertAccessControl(sFoundSample, dOrders[sFoundSample]['accession'])
            CreateAccessInfo(dAccess['token'], dAccess['password'])
      uPhase = 0
      bInput = True
      continue
    if uPhase == 1010:
      print("")
      sInput = input("Nome: ").strip().upper()
      if sInput.lower() == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        sName = sInput
        uPhase = 1011
    if uPhase == 1011:
      print("")
      sInput = input("Amostra (NC) ou nome do ficheiro PDF sem extensao: ").strip().lower()
      if sInput.lower() == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        sFile = sInput
        uPhase = 1012
    if uPhase == 1012:
      print("")
      sInput = input("Email: ").strip().lower()
      if sInput.lower() == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        sEmail = sInput
        tRes = ("email_2", sName, sFile, sEmail)
        bInput = False
  return tRes

def InputSelectSampleOptionPage1(sSample, uCurrTestIndex, uLenTests):
  global dTests, sDateFormat, sStandardDateFormat
  sEmailRE = r"^[A-Za-z0-9\.\+_-]+@[A-Za-z0-9\._-]+\.[a-zA-Z]*$"
  tRes = None
  bInput = True
  uPhase = 0
  while bInput:
    if uPhase == 0:
      os.system("cls")
      print("A apresentar teste {} de {}".format(str(uCurrTestIndex + 1), str(uLenTests)))
      print("")
      PrintTest(sSample)
      print("Selecione umas das seguintes opcoes:")
      print("- 1: Teste seguinte;")
      print("- 2: Teste anterior;")
      print("- 3: Enviar email;")
      print("- 4: Transmissao por contacto telefonico;")
      print("- 5: Imprimir boletim;")
      print("- 6: Cancelar transmissao;")
      print("- 7: Outras opcoes;")
      print("- S: Sair;")
      print("- Use <C> para cancelar.")
      print("")
      sInput = input("Opcao: ").strip().lower()
      if sInput == "c":
        bInput = False
      if sInput == "s":
        uPhase = 50
      if sInput == "3":
        uPhase = 10
      if sInput == "1":
        uPhase = 20
      if sInput == "2":
        uPhase = 30
      if sInput == "4":
        uPhase = 40
      if sInput == "5":
        uPhase = 60
      if sInput == "6":
        uPhase = 70
      if sInput == "7":
        uPhase = 80
    if uPhase == 10:
      print("")
      sInput = input("Email (numero ou endereco): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "s":
        uPhase = 50
      elif sInput == "":
        continue
      else:
        uPhase = 11
    if uPhase == 11:
      if IsInt(sInput):
        uPhase = 12
      else:
        sEmail = RemoveAccents(sInput)
        if re.match(sEmailRE, sEmail):
          uPhase = 13
        else:
          print("Atencao: Indique um endereco de email valido.")
          uPhase = 10
    if uPhase == 12:
      uEmail = int(sInput)
      dUnsentEmails = dict()
      uIndex = 0
      for dRow in dTests[sSample]['transmissions']:
        if dRow['class'] == "email" and (dRow['status'] == "pending" or dRow['status'] == "error"):
          dUnsentEmails[dRow['index']] = uIndex
        uIndex = uIndex + 1
      if uEmail not in dUnsentEmails:
        print("Atencao: Indique um numero valido ou um endereco.")
        uPhase = 10
      elif dTests[sSample]['transmissions'][dUnsentEmails[uEmail]]['recipient_class'] in ["manager", "caregiver"] and dTests[sSample]['result'] not in ['negative', 'error']:
        print("Atencao: Transmissao de resultado positivo ou inconclusivo impedida.")
        uPhase = 10
      else:
        tRes = ("email", {"address": dTests[sSample]['transmissions'][dUnsentEmails[uEmail]]['description'], "class": dTests[sSample]['transmissions'][dUnsentEmails[uEmail]]['recipient_class']})
        bInput = False
    if uPhase == 13:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Email do proprio;")
      print("- 2: Email de clinico;")
      print("- 3: Email institucional;")
      print("- 4: Email de cuidador;")
      print("- 5: Email de elemento de familia.")
      print("")
      sInput = input("Tipo de email: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "s":
        uPhase = 50
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "patient",
          "2": "clinician",
          "3": "manager",
          "4": "caregiver",
          "5": "family"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique um tipo de email valido.")
        elif sInput in ["3", "4"] and dTests[sSample]['result'] not in ['negative', 'error']:
          print("Atencao: Transmissao de resultado positivo ou inconclusivo impedida.")
        else:
          tRes = ("email", {"address": sEmail, "class": dClassTrans[sInput]})
          bInput = False
    if uPhase == 20:
      tRes = ("next", None)
      bInput = False
    if uPhase == 30:
      tRes = ("previous", None)
      bInput = False
    if uPhase == 40:
      print("")
      sInput = input("Numero: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "s":
        uPhase = 50
      elif sInput == "":
        continue
      else:
        sContact = sInput
        uPhase = 41
    if uPhase == 41:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Contacto do proprio;")
      print("- 2: Contacto de clinico;")
      print("- 3: Contacto institucional;")
      print("- 4: Contacto de cuidador;")
      print("- 5: Contacto de elemento de familia.")
      print("")
      sInput = input("Tipo de contacto: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "s":
        uPhase = 50
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "patient",
          "2": "clinician",
          "3": "manager",
          "4": "caregiver",
          "5": "family"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique um tipo de contacto valido.")
        elif sInput in ["3", "4"] and dTests[sSample]['result'] not in ['negative', 'error']:
          print("Atencao: Transmissao de resultado positivo ou inconclusivo impedida.")
        else:
          sClass = dClassTrans[sInput]
          uPhase = 42
    if uPhase == 42:
      if sClass not in ["patient"]:
        print("")
        sInput = input("Destinatario ou <ENTER>: ").strip()
        if sInput.lower() == "c":
          uPhase = 0
        else:
          uPhase = 43
      else:
        sInput = ""
        uPhase = 43
    if uPhase == 43:
      sRecipient = sInput if sInput != "" else "void"
      tRes = ("contact", {"contact": sContact, "class": sClass, "recipient_description": sRecipient})
      bInput = False
    if uPhase == 60:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Impressao para o proprio;")
      print("- 2: Impressao para clinico;")
      print("- 3: Impressao para instituicao;")
      print("- 4: Impressao para cuidador;")
      print("- 5: Impressao para elemento de familia.")
      print("")
      sInput = input("Tipo de impressao: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "s":
        uPhase = 50
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "patient",
          "2": "clinician",
          "3": "manager",
          "4": "caregiver",
          "5": "family"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique um tipo de impressao valido.")
        elif sInput in ["3", "4"] and dTests[sSample]['result'] not in ['negative', 'error']:
          print("Atencao: Transmissao de resultado positivo ou inconclusivo impedida.")
        else:
          sClass = dClassTrans[sInput]
          uPhase = 61
    if uPhase == 61:
      if sClass not in ["patient"]:
        print("")
        sInput = input("Destinatario ou <ENTER>: ").strip()
        if sInput.lower() == "c":
          uPhase = 0
        else:
          uPhase = 62
      else:
        sInput = ""
        uPhase = 62
    if uPhase == 62:
      sRecipient = sInput if sInput != "" else "void"
      tRes = ("print", {"description": "void", "class": sClass, "recipient_description": sRecipient})
      bInput = False
    if uPhase == 70:
      print("")
      sInput = input("Transmissao (numero ou <P> para o proprio): ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "s":
        uPhase = 50
      elif sInput == "":
        continue
      elif sInput == "p":
        tRes = ("cancel_own", None)
        bInput = False
      elif not IsInt(sInput):
        print("Atencao: Indique um numero valido ou <P>.")
      else:
        ldTransmissions = dTests[sSample]['transmissions']
        uCount = len(ldTransmissions)
        if uCount == 0:
          print("Atencao: Nao existem transmissoes para este teste.")
          continue
        uInput = int(sInput)
        dTransmissions = dict()
        uIndex = 0
        for dRow in ldTransmissions:
          if dRow['status'] != "canceled":
            dTransmissions[dRow['index']] = uIndex
          uIndex = uIndex + 1
        if uInput not in dTransmissions:
          print("Atencao: Indique um numero valido ou <P>.")
          uPhase = 70
        else:
          tRes = ("cancel", {
            'index': dTransmissions[uInput],
            'id': dTests[sSample]['transmissions'][dTransmissions[uInput]]['id'],
            'type': dTests[sSample]['transmissions'][dTransmissions[uInput]]['type']
          })
          bInput = False
    if uPhase == 80:
      tRes = ("page2", None)
      bInput = False
    if uPhase == 50:
      tRes = ("exit", None)
      bInput = False
  return tRes

def InputSelectSampleOptionPage2(sSample, uCurrTestIndex, uLenTests):
  global dTests, sDateFormat, sStandardDateFormat
  sEmailRE = r"^[A-Za-z0-9\.\+_-]+@[A-Za-z0-9\._-]+\.[a-zA-Z]*$"
  lsVoid = ["null", "NULL", "void", "VOID", "", None]
  sREToken = r"^[A-Z]\d{5}$"
  tRes = None
  bInput = True
  uPhase = 0
  while bInput:
    if uPhase == 0:
      os.system("cls")
      print("A apresentar teste {} de {}".format(str(uCurrTestIndex + 1), str(uLenTests)))
      print("")
      PrintTest(sSample)
      print("Selecione umas das seguintes opcoes (pagina 2):")
      print("- 1: Eliminar transmissao;")
      print("- 2: Alterar estado da transmissao;")
      print("- 3: Registar email;")
      print("- 4: Registar contacto telefonico;")
      print("- 5: Registar impressao;")
      print("- 6: Criar senha de acesso;")
      print("- 7: Impressao interna.")
      print("- Use <C> para cancelar.")
      print("")
      sInput = input("Opcao: ").strip().lower()
      if sInput == "c":
        bInput = False
      if sInput == "1":
        uPhase = 10
      if sInput == "2":
        uPhase = 20
      if sInput == "3":
        uPhase = 30
      if sInput == "4":
        uPhase = 40
      if sInput == "5":
        uPhase = 50
      if sInput == "6":
        uPhase = 60
      if sInput == "7":
        uPhase = 70
    if uPhase == 10:
      print("")
      ldTransmissions = dTests[sSample]['transmissions']
      uCount = len(ldTransmissions)
      if uCount == 0:
        print("Atencao: Nao existem transmissoes para este teste.")
        uPhase = 0
        continue
      sInput = input("Transmissao: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      elif not IsInt(sInput):
        print("Atencao: Indique um numero valido.")
      else:
        uInput = int(sInput)
        dTransmissions = dict()
        uIndex = 0
        for dRow in ldTransmissions:
          dTransmissions[dRow['index']] = uIndex
          uIndex = uIndex + 1
        if uInput not in dTransmissions:
          print("Atencao: Indique um numero valido.")
          uPhase = 10
        elif dTests[sSample]['transmissions'][dTransmissions[uInput]]['type'] == "email":
          print("Atencao: Esta transmissao nao pode ser eliminada. Se for necessario, cancele-a.")
          uPhase = 10
        elif dTests[sSample]['transmissions'][dTransmissions[uInput]]['type'] == "internal":
          print("Atencao: Esta transmissao nao pode ser eliminada. Se for necessario, cancele-a.")
          uPhase = 10
        else:
          tRes = ("delete", {'id': dTests[sSample]['transmissions'][dTransmissions[uInput]]['id']})
          bInput = False
    if uPhase == 20:
      print("")
      ldTransmissions = dTests[sSample]['transmissions']
      uCount = len(ldTransmissions)
      if uCount == 0:
        print("Atencao: Nao existem transmissoes para este teste.")
        uPhase = 0
        continue
      sInput = input("Transmissao: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      elif not IsInt(sInput):
        print("Atencao: Indique um numero valido.")
      else:
        uInput = int(sInput)
        dTransmissions = dict()
        uIndex = 0
        for dRow in ldTransmissions:
          dTransmissions[dRow['index']] = uIndex
          uIndex = uIndex + 1
        if uInput not in dTransmissions:
          print("Atencao: Indique um numero valido.")
          uPhase = 20
        else:
          uPhase = 21
    if uPhase == 21:
      uTransmission = uInput
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Transmissao OK;")
      print("- 2: Transmissao pendente;")
      print("- 3: Transmissao com erro;")
      print("- 4: Transmissao cancelada.")
      print("")
      sInput = input("Estado da transmissao: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dStatusTrans = {
          "1": "ok",
          "2": "pending",
          "3": "error",
          "4": "canceled"
        }
        if sInput not in dStatusTrans:
          print("Atencao: Indique um estado de transmissao valido.")
        else:
          tRes = ("status", {
            'index': dTransmissions[uTransmission],
            'id': dTests[sSample]['transmissions'][dTransmissions[uTransmission]]['id'],
            'status': dStatusTrans[sInput], 
            'type': dTests[sSample]['transmissions'][dTransmissions[uTransmission]]['type']})
          bInput = False
    if uPhase == 30:
      print("")
      sInput = input("Email: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        sEmail = RemoveAccents(sInput)
        if re.match(sEmailRE, sEmail):
          uPhase = 31
        else:
          print("Atencao: Indique um endereco de email valido.")
          uPhase = 30
    if uPhase == 31:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Email do proprio;")
      print("- 2: Email de clinico;")
      print("- 3: Email institucional;")
      print("- 4: Email de cuidador;")
      print("- 5: Email de elemento de familia.")
      print("")
      sInput = input("Tipo de email: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "patient",
          "2": "clinician",
          "3": "manager",
          "4": "caregiver",
          "5": "family"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique um tipo de email valido.")
        else:
          sClass = dClassTrans[sInput]
          uPhase = 32
    if uPhase == 32:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Transmissao OK;")
      print("- 2: Transmissao pendente;")
      print("- 3: Transmissao com erro;")
      print("- 4: Transmissao cancelada.")
      print("")
      sInput = input("Estado da transmissao: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dStatusTrans = {
          "1": "ok",
          "2": "pending",
          "3": "error",
          "4": "canceled"
        }
        if sInput not in dStatusTrans:
          print("Atencao: Indique um estado de transmissao valido.")
        else:
          tRes = (
            "add",
            {
              'class': "email",
              'description': sEmail,
              'recipient_class': sClass,
              'recipient_description': "void",
              'status': dStatusTrans[sInput]
            }
          )
          bInput = False
    if uPhase == 40:
      print("")
      sInput = input("Contacto telefonico: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        sContact = sInput
        uPhase = 41
    if uPhase == 41:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Contacto do proprio;")
      print("- 2: Contacto de clinico;")
      print("- 3: Contacto institucional;")
      print("- 4: Contacto de cuidador;")
      print("- 5: Contacto de elemento de familia.")
      print("")
      sInput = input("Tipo de contacto: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "patient",
          "2": "clinician",
          "3": "manager",
          "4": "caregiver",
          "5": "family"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique um tipo de contacto valido.")
        else:
          sClass = dClassTrans[sInput]
          uPhase = 42
    if uPhase == 42:
      if sClass not in ["patient"]:
        print("")
        sInput = input("Destinatario ou <ENTER>: ").strip()
        if sInput.lower() == "c":
          uPhase = 0
        else:
          sRecipient = sInput if sInput != "" else "void"
          uPhase = 43
      else:
        sInput = ""
        sRecipient = sInput if sInput != "" else "void"
        uPhase = 43
    if uPhase == 43:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Transmissao OK;")
      print("- 2: Transmissao pendente;")
      print("- 3: Transmissao com erro;")
      print("- 4: Transmissao cancelada.")
      print("")
      sInput = input("Estado da transmissao: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dStatusTrans = {
          "1": "ok",
          "2": "pending",
          "3": "error",
          "4": "canceled"
        }
        if sInput not in dStatusTrans:
          print("Atencao: Indique um estado de transmissao valido.")
        else:
          tRes = (
            "add",
            {
              'class': "phone",
              'description': sContact,
              'recipient_class': sClass,
              'recipient_description': sRecipient,
              'status': dStatusTrans[sInput]
            }
          )
          bInput = False
    if uPhase == 50:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Impressao para o proprio;")
      print("- 2: Impressao para clinico;")
      print("- 3: Impressao para instituicao;")
      print("- 4: Impressao para cuidador;")
      print("- 5: Impressao para elemento de familia.")
      print("")
      sInput = input("Tipo de impressao: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "patient",
          "2": "clinician",
          "3": "manager",
          "4": "caregiver",
          "5": "family"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique um tipo de impressao valido.")
        else:
          sClass = dClassTrans[sInput]
          uPhase = 51
    if uPhase == 51:
      if sClass not in ["patient"]:
        print("")
        sInput = input("Destinatario ou <ENTER>: ").strip()
        if sInput.lower() == "c":
          uPhase = 0
        else:
          sRecipient = sInput if sInput != "" else "void"
          uPhase = 52
      else:
        sInput = ""
        sRecipient = sInput if sInput != "" else "void"
        uPhase = 52
    if uPhase == 52:
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Transmissao OK;")
      print("- 2: Transmissao pendente;")
      print("- 3: Transmissao com erro;")
      print("- 4: Transmissao cancelada.")
      print("")
      sInput = input("Estado da transmissao: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dStatusTrans = {
          "1": "ok",
          "2": "pending",
          "3": "error",
          "4": "canceled"
        }
        if sInput not in dStatusTrans:
          print("Atencao: Indique um estado de transmissao valido.")
        else:
          tRes = (
            "add",
            {
              'class': "print",
              'description': "void",
              'recipient_class': sClass,
              'recipient_description': sRecipient,
              'status': dStatusTrans[sInput]
            }
          )
          bInput = False
    if uPhase == 60:
      print("")
      sInput = input("Numero de amostra (NT): ").strip().lower()[0:6]
      if sInput == "c":
        uPhase = 0
      elif sInput != "" and re.match(sREToken, sInput.upper()):
        uPhase = 61
      else:
        print("Atencao: Indique um numero de amostra (NT) valido.")
        uPhase = 60
    if uPhase == 61:
      tRes = ("create_access", sInput.upper())
      bInput = False
    if uPhase == 70:
      sRecipient = ""
      if 'internal' in dTests[sSample]:
        sRecipient = dTests[sSample]['internal']['title'] if dTests[sSample]['internal']['title'] not in lsVoid else dTests[sSample]['internal']['department']
        uPhase = 72
      else:
        print("")
        uPhase = 71
    if uPhase == 71:
      sInput = input("Departamento: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        sRecipient = sInput
        uPhase = 72
    if uPhase == 72:
      tRes = ("print_internal", {"description": "void", "recipient_description": sRecipient})
      bInput = False
  return tRes

def PrintTransmission(uTransmissionIndex, sClass, sDescription, sRecipientClass, sRecipientDescription, sDatetime):
  print("{}. {}{} ({}{}) {}".format(
    str(uTransmissionIndex),
    GetTransmissionClass(sClass),
    " {}".format(sDescription) if sDescription.lower() != "void" else "",
    GetRecipientClass(sRecipientClass),
    " - {}".format(sRecipientDescription) if sRecipientDescription.lower() != "void" else "",
    sDatetime)
  )
 
def PrintTest(sSample):
  global dTests
  dTest = dTests[sSample]
  lsVoid = ["", "NULL", "null", "Null", None]
  print("Nome: {}".format(dTest['name']))
  print("DN: {} | #STATE_ID_1#: {}".format(
    ConvertDatetime(dTest['birthday'], sStandardDateFormat, sDateFormat),
    dTest['state_id']
    ))
  print("")
  print("(A) {}{} | (P) {} | (S) {}".format(
    sSample,
    " ({})".format(dTest['accession']) if dTest['accession'] else "",
    dTest['record'],
    dTest['department']
    ))
  print("")
  print("Resultado: {} {}".format(GetResult(dTest['result'].lower()), "({})".format(dTest['result_datetime']) if dTest['result_datetime'] not in lsVoid else ""))
  if 'password' in dTest:
    print("")
    print("Senha de acesso: {}".format(dTest['password'].upper()))
  if 'group' in dTest:
    print("")
    print("Grupo: {}\n- Tipo: {}{}{}{}".format(
      dTest['group']['title'],
      GetGroupPatientClass(dTest['group']['class']),
      " | Departmento: {}".format(dTest['group']['department']) if dTest['group']['department'] not in lsVoid else "",
      " | Categoria: {}".format(dTest['group']['category']) if dTest['group']['category'] not in lsVoid else "",
      " | Localizacao: {}".format(dTest['group']['location']) if dTest['group']['location'] not in lsVoid else ""
      ))
  bOKList = False
  bPendingList = False
  bErrorList = False
  bCanceledList = False
  uTransmissionIndex = 1
  for dRow in dTest['transmissions']:
    if dRow['status'] == "pending":
      if not bPendingList:
        print("")
        print("Transmissoes pendentes:")
        bPendingList = True
      PrintTransmission(uTransmissionIndex, dRow['class'], dRow['description'], dRow['recipient_class'], dRow['recipient_description'], dRow['datetime'])
      dRow['index'] = uTransmissionIndex
      uTransmissionIndex = uTransmissionIndex + 1
  for dRow in dTest['transmissions']:
    if dRow['status'] == "error":
      if not bErrorList:
        print("")
        print("Transmissoes com erro:")
        bPendingList = True
      PrintTransmission(uTransmissionIndex, dRow['class'], dRow['description'], dRow['recipient_class'], dRow['recipient_description'], dRow['datetime'])
      dRow['index'] = uTransmissionIndex
      uTransmissionIndex = uTransmissionIndex + 1
  for dRow in dTest['transmissions']:
    if dRow['status'] == "ok":
      if not bOKList:
        print("")
        print("Transmissoes concluidas:")
        bOKList = True
      PrintTransmission(uTransmissionIndex, dRow['class'], dRow['description'], dRow['recipient_class'], dRow['recipient_description'], dRow['datetime'])
      dRow['index'] = uTransmissionIndex
      uTransmissionIndex = uTransmissionIndex + 1
  for dRow in dTest['transmissions']:
    if dRow['status'] == "canceled":
      if not bCanceledList:
        print("")
        print("Transmissoes canceladas:")
        bCanceledList = True
      PrintTransmission(uTransmissionIndex, dRow['class'], dRow['description'], dRow['recipient_class'], dRow['recipient_description'], dRow['datetime'])
      dRow['index'] = uTransmissionIndex
      uTransmissionIndex = uTransmissionIndex + 1
  print("")

def PrintTestEmailAccess(sSample, sEmail, sEmailClass):
  global dTests
  dTest = dTests[sSample]
  lsVoid = ["", "NULL", "null", "Null", None]
  print("Enviar resultado por email")
  print("--------------------------")
  print("")
  print("- Nome: {}".format(dTest['name']))
  print("- DN: {}".format(ConvertDatetime(dTest['birthday'], sStandardDateFormat, sDateFormat)))
  print("- #STATE_ID_1#: {}".format(dTest['state_id']))
  print("")
  print("- Email: {}".format(sEmail))
  print("- Tipo de email: {}".format(GetRecipientClass(sEmailClass)))
  print("")
  print("- (A) {}{} | (P) {} | (S) {}".format(
    sSample,
    " ({})".format(dTest['accession']) if dTest['accession'] else "",
    dTest['record'],
    dTest['department']
    ))
  print("")
  print("- Resultado: {} {}".format(GetResult(dTest['result'].lower()), "({})".format(dTest['result_datetime']) if dTest['result_datetime'] not in lsVoid else ""))
  if 'password' in dTest:
    print("")
    print("- Senha de acesso: {}".format(dTest['password'].upper()))
  if 'group' in dTest:
    print("")
    print("- Grupo: {}\n-   Tipo: {}{}{}{}".format(
      dTest['group']['title'],
      GetGroupPatientClass(dTest['group']['class']),
      " | Departmento: {}".format(dTest['group']['department']) if dTest['group']['department'] not in lsVoid else "",
      " | Categoria: {}".format(dTest['group']['category']) if dTest['group']['category'] not in lsVoid else "",
      " | Localizacao: {}".format(dTest['group']['location']) if dTest['group']['location'] not in lsVoid else ""
      ))
  print("")
  bInput = True
  bSend = False
  while bInput:
    sInput = input("Enviar resultado por email (s/n): ").strip().lower()
    if sInput == "s":
      bSend = True
      bInput = False
    elif sInput == "n":
      bSend = False
      bInput = False
  return bSend

def CycleTests(lsSamples):
  global dTests
  sRes = None
  uIndex = 0
  bCycle = True
  while bCycle:
    os.system("cls")
    print("A apresentar teste {} de {}".format(str(uIndex + 1), str(len(lsSamples))))
    print("")
    sSample = lsSamples[uIndex]
    dRow = dTests[sSample]
    bEmailOwnSent = False
    bPhoneContact = False
    bPrinted = False
    bPrintDisabled = False
    PrintTest(sSample)
    tOption = InputSelectSampleOptionPage1(sSample, uIndex, len(lsSamples))
    if not tOption:
      bCycle = False
      sRes = "cancel"
      continue
    if tOption[0] == "page2":
      os.system("cls")
      print("A apresentar teste {} de {}".format(str(uIndex + 1), str(len(lsSamples))))
      print("")
      sSample = lsSamples[uIndex]
      dRow = dTests[sSample]
      bEmailOwnSent = False
      bPhoneContact = False
      bPrinted = False
      bPrintDisabled = False
      PrintTest(sSample)
      tOption = InputSelectSampleOptionPage2(sSample, uIndex, len(lsSamples))
      if not tOption:
        continue
    if tOption[0] == "email":
      dEmail = tOption[1]
      try:
        SendEmail(sSample, dEmail['class'], [dEmail['address']])
      except:
        print("Erro: Nao foi possivel enviar o email. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "contact":
      dContact = tOption[1]
      try:
        DBInsertTransmission(
          sSample,
          "phone",
          dContact['contact'],
          dContact['class'],
          dContact['recipient_description'],
          "ok"
          )
      except:
        print("Erro: Nao foi possivel registar o contacto. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "print":
      dPrint = tOption[1]
      try:
        PrintResult(sSample)
        DBInsertTransmission(
          sSample,
          "print",
          dPrint['description'],
          dPrint['class'],
          dPrint['recipient_description'],
          "ok"
          )
      except:
        print("Erro: Nao foi possivel registar a impressao. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "print_internal":
      dPrint = tOption[1]
      try:
        PrintResult(sSample)
        DBInsertTransmission(
          sSample,
          "internal",
          dPrint['description'],
          "clinician",
          dPrint['recipient_description'],
          "ok"
          )
      except:
        print("Erro: Nao foi possivel registar a impressao interna. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "next":
      if uIndex + 1 >= len(lsSamples):
        print("Atencao: Ja chegou ao fim da lista.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        uIndex = uIndex + 1
    if tOption[0] == "previous":
      if uIndex <= 0:
        print("Atencao: Ja esta no inicio da lista.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        uIndex = uIndex - 1
    if tOption[0] == "cancel":
      uOptionIndex = tOption[1]['index']
      sID = tOption[1]['id']
      sType = tOption[1]['type']
      try:
        if sType == "transmission":
          DBUpdateTransmission(
            sID,
            dRow['transmissions'][uOptionIndex]['class'],
            dRow['transmissions'][uOptionIndex]['description'],
            dRow['transmissions'][uOptionIndex]['recipient_class'],
            dRow['transmissions'][uOptionIndex]['recipient_description'],
            "canceled"
          )
        elif sType == "email":
          DBInsertTransmission(
            sSample,
            dRow['transmissions'][uOptionIndex]['class'],
            dRow['transmissions'][uOptionIndex]['description'],
            dRow['transmissions'][uOptionIndex]['recipient_class'],
            dRow['transmissions'][uOptionIndex]['recipient_description'],
            "canceled"
          )
        elif sType == "internal":
          DBInsertTransmission(
            sSample,
            dRow['transmissions'][uOptionIndex]['class'],
            dRow['transmissions'][uOptionIndex]['description'],
            dRow['transmissions'][uOptionIndex]['recipient_class'],
            dRow['transmissions'][uOptionIndex]['recipient_description'],
            "canceled"
          )
        else:
          raise
      except:
        print("Erro: Nao foi possivel atualizar a transmissao. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "cancel_own":
      try:
        DBInsertTransmission(
          sSample,
          "unknown",
          "void",
          "patient",
          "void",
          "canceled"
          )
      except:
        print("Erro: Nao foi possivel introduzir a transmissao. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "delete":
      sID = tOption[1]['id']
      try:
        DBDeleteTransmission(sID)
      except:
        print("Erro: Nao foi possivel eliminar a transmissao. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "status":
      uOptionIndex = tOption[1]['index']
      sID = tOption[1]['id']
      sType = tOption[1]['type']
      sStatus = tOption[1]['status']
      try:
        if sType == "transmission":
          DBUpdateTransmission(
            sID,
            dRow['transmissions'][uOptionIndex]['class'],
            dRow['transmissions'][uOptionIndex]['description'],
            dRow['transmissions'][uOptionIndex]['recipient_class'],
            dRow['transmissions'][uOptionIndex]['recipient_description'],
            sStatus
          )
        elif sType == "email":
          DBInsertTransmission(
            sSample,
            dRow['transmissions'][uOptionIndex]['class'],
            dRow['transmissions'][uOptionIndex]['description'],
            dRow['transmissions'][uOptionIndex]['recipient_class'],
            dRow['transmissions'][uOptionIndex]['recipient_description'],
            sStatus
          )
        elif sType == "internal":
          DBInsertTransmission(
            sSample,
            dRow['transmissions'][uOptionIndex]['class'],
            dRow['transmissions'][uOptionIndex]['description'],
            dRow['transmissions'][uOptionIndex]['recipient_class'],
            dRow['transmissions'][uOptionIndex]['recipient_description'],
            sStatus
          )
        else:
          raise
      except Exception as dError:
        print("Erro: Nao foi possivel atualizar a transmissao. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "add":
      try:
        DBInsertTransmission(
          sSample,
          tOption[1]['class'],
          tOption[1]['description'],
          tOption[1]['recipient_class'],
          tOption[1]['recipient_description'],
          tOption[1]['status']
        )
      except Exception as dError:
        print("Erro: Nao foi possivel atualizar a transmissao. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "create_access":
      dAccess = DBInsertAccessControl(sSample, tOption[1])
      CreateAccessInfo(dAccess['token'], dAccess['password'])
      UpdateTests(None, None, None, None, None, sSample, None, False)
    if tOption[0] == "exit":
      bCycle = False
      sRes = "exit"
      continue
  return sRes

def PrintGroup(tsGroup, lsGroupIDs, dGroupIDs):
  print("- Título: {}".format(tsGroup[0]))
  print("- Categoria: {}".format(GetGroupClass(tsGroup[1])))
  print("- Amostras: {}".format(len(lsGroupIDs)))
  for sKey, lsValues in dGroupIDs.items():
    print("  - {}: {}".format(GetGroupPatientClassPlural(sKey), len(lsValues)))
  return

def CycleGroups(dGroups, sDatetimeStart, sDatetimeEnd, sSearchTitle, uStartIndex):
  global sStandardDatetimeFormat, sDateFormat, uGroupListPageLen
  tsRes = None
  uIndex = uStartIndex
  bCycle = True
  ltKeys = list(dGroups.keys())
  if uIndex >= len(dGroups):
    uIndex = len(dGroups) - 1
  uPhase = 0
  while bCycle:
    lsGroupIDs = dGroups[ltKeys[uIndex]][0]
    dGroupIDs = dGroups[ltKeys[uIndex]][1]
    if uPhase == 0:
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Gerir grupos")
      print("")
      print("- Inicio: {}".format(datetime.datetime.strptime(sDatetimeStart, sStandardDatetimeFormat).strftime(sDateFormat)))
      print("- Fim: {}".format(datetime.datetime.strptime(sDatetimeEnd, sStandardDatetimeFormat).strftime(sDateFormat)))
      if sSearchTitle:
        print("- Titulo pesquisado: {}".format(sSearchTitle))
      print("")
      print("A apresentar grupo {} de {}".format(str(uIndex + 1), str(len(dGroups))))
      print("")
      PrintGroup(ltKeys[uIndex], lsGroupIDs, dGroupIDs)
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Grupo seguinte;")
      print("- 2: Grupo anterior;")
      print("- 3: Modificar titulo;")
      print("- 4: Modificar categoria;")
      print("- 5: Eliminar;")
      print("- 6: Mostrar lista;")
      print("- 7: Mostrar amostras.")
      print("")
      print("Use <C> para cancelar.")
      print("")
      sInput = input("Opcao: ").strip().lower()
      if sInput == "c":
        bCycle = False
      if sInput == "1":
        if uIndex + 1 >= len(dGroups):
          print("Atencao: Ja chegou ao fim da lista.")
          print("")
          input("Pressione <ENTER> para continuar...")
        else:
          uIndex = uIndex + 1
      if sInput == "2":
        if uIndex == 0:
          print("Atencao: Ja esta no inicio da lista.")
          print("")
          input("Pressione <ENTER> para continuar...")
        else:
          uIndex = uIndex - 1
      if sInput == "3":
        uPhase = 30
      if sInput == "4":
        uPhase = 40
      if sInput == "5":
        uPhase = 50
      if sInput == "6":
        uPhase = 60
    if uPhase == 30:
      print("")
      sInput = " ".join(input("Titulo: ").strip().split())
      if sInput.lower() == "c":
        uPhase = 0
      elif not sInput:
        print("Atencao: Indique um titulo.")
      else:
        sNewTitle = sInput
        uPhase = 31
    if uPhase == 31:
       print("")
       sInput = input("Modificar em todas as datas (s/n)? ").strip().lower()
       if sInput == "c":
         uPhase = 0
       elif sInput == "s":
         bAllTime = True
         uPhase = 32
       elif sInput == "n":
         bAllTime = False
         uPhase = 32
       else:
         continue
    if uPhase == 32:
      tsRes = ("update_title", (ltKeys[uIndex], sDatetimeStart, sDatetimeEnd, bAllTime, sNewTitle, lsGroupIDs), uIndex)
      bCycle = False
    if uPhase == 40:
      print("")
      print("Indique a categoria:")
      print("- 1: ERPI;")
      print("- 2: Instituicao de ensino;")
      print("- 3: Lar infantil;")
      print("- 4: Unidade de saude;")
      print("- 5: Outra.")
      print("")
      sInput = input("Categoria de grupo: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "nursing_home",
          "2": "school",
          "3": "children_home",
          "4": "healthcare_unit",
          "5": "other"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique uma categoria valida.")
        else:
          sNewClass = dClassTrans[sInput]
          uPhase = 41
    if uPhase == 41:
       print("")
       sInput = input("Modificar em todas as datas (s/n)? ").strip().lower()
       if sInput == "c":
         uPhase = 0
       elif sInput == "s":
         bAllTime = True
         uPhase = 42
       elif sInput == "n":
         bAllTime = False
         uPhase = 42
       else:
         continue
    if uPhase == 42:
      tsRes = ("update_class", (ltKeys[uIndex], sDatetimeStart, sDatetimeEnd, bAllTime, sNewClass, lsGroupIDs), uIndex)
      bCycle = False
    if uPhase == 50:
       print("")
       sInput = input("Eliminar em todas as datas (s/n)? ").strip().lower()
       if sInput == "c":
         uPhase = 0
       elif sInput == "s":
         bAllTime = True
         uPhase = 51
       elif sInput == "n":
         bAllTime = False
         uPhase = 51
       else:
         continue
    if uPhase == 51:
      tsRes = ("delete", (ltKeys[uIndex], sDatetimeStart, sDatetimeEnd, bAllTime, lsGroupIDs), uIndex)
      bCycle = False
    if uPhase == 60:
      uPages = math.ceil(len(ltKeys) / uGroupListPageLen)
      uCurrPage = 0
      uPhase = 61
    if uPhase == 61:
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("A listar grupos na página {} de {} ({} a {})".format(str(uCurrPage + 1), str(uPages), datetime.datetime.strptime(sDatetimeStart, sStandardDatetimeFormat).strftime(sDateFormat),
datetime.datetime.strptime(sDatetimeEnd, sStandardDatetimeFormat).strftime(sDateFormat)))
      for uGroupIndex, tKey in enumerate(ltKeys):
        uGroupPage = math.floor(uGroupIndex / uGroupListPageLen)
        if uGroupPage != uCurrPage:
          continue
        sCounts = ""
        for sGroupPatientClass, lsGroupIDs in dGroups[tKey][1].items():
          sCounts = "{}{} {}".format("{}; ".format(sCounts) if sCounts else "", str(len(lsGroupIDs)), GetGroupPatientClassPlural(sGroupPatientClass).lower() if len(lsGroupIDs) != 1 else GetGroupPatientClass(sGroupPatientClass).lower())
        sGroupTitle = tKey[0]
        sGroupClass = GetGroupClass2(tKey[1])
        # print("\n# Grupo {} - {} ({}):\n  {}".format(str(uGroupIndex + 1), sGroupClass, sCounts, sGroupTitle))
        print("\n#{}: {} ({})".format(str(uGroupIndex + 1), sGroupTitle, sGroupClass))
      sInput = input("\nOpcao (numero, S/P, A/V ou C): ").strip().lower()
      if IsInt(sInput):
        uGroupSelected = int(sInput)
        uPhase = 62
      elif sInput in ["s", "p"]:
        uCurrPage = uCurrPage + 1
        if uCurrPage >= uPages:
          uCurrPage = uPages - 1
        uPhase = 61
      elif sInput in ["a", "v"]:
        if uCurrPage > 0:
          uCurrPage = uCurrPage - 1
        uPhase = 61
      elif sInput == "c":
        uPhase = 0
      else:
        continue
    if uPhase == 62:
      if uGroupSelected > 0 and uGroupSelected <= len(ltKeys):
        uIndex = uGroupSelected - 1
        uPhase = 0
      else:
        input("\nAtencao: Selecione uma opcao valida.\n\nPressione <ENTER> para continuar...")
        uPhase = 60
  return tsRes

def PrintGroupSample(dSample):
  print("- Nome: {}".format(dSample['name']))
  print("- Data de nascimento: {}".format(dSample['birthday']))
  return

def CycleGroupSamples(dGroup, sGroupTitle, sGroupClass, sDatetimeStart, sDatetimeEnd, uStartIndex):
  global sStandardDatetimeFormat, sDateFormat, uGroupListPageLen
  tsRes = None
  lsSamples = list(dGroup[2].keys())
  uIndex = uStartIndex
  bCycle = True
  if uIndex >= len(dGroup[2]):
    uIndex = len(dGroup[2]) - 1
  uPhase = 0
  while bCycle:
    if uPhase == 0:
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("Gerir grupos | Lista de amostras")
      print("")
      print("- Inicio: {}".format(datetime.datetime.strptime(sDatetimeStart, sStandardDatetimeFormat).strftime(sDateFormat)))
      print("- Fim: {}".format(datetime.datetime.strptime(sDatetimeEnd, sStandardDatetimeFormat).strftime(sDateFormat)))
      print("")
      print("- Grupo: {}".format(dGroup))
      print("")
      print("A apresentar amostra {} de {}".format(str(uIndex + 1), str(len(dGroups))))
      print("")
      PrintGroup(ltKeys[uIndex], lsGroupIDs, dGroupIDs)
      print("")
      print("Selecione uma das seguintes opcoes:")
      print("- 1: Grupo seguinte;")
      print("- 2: Grupo anterior;")
      print("- 3: Modificar titulo;")
      print("- 4: Modificar categoria;")
      print("- 5: Eliminar;")
      print("- 6: Mostrar lista;")
      print("- 7: Mostrar amostras.")
      print("")
      print("Use <C> para cancelar.")
      print("")
      sInput = input("Opcao: ").strip().lower()
      if sInput == "c":
        bCycle = False
      if sInput == "1":
        if uIndex + 1 >= len(dGroups):
          print("Atencao: Ja chegou ao fim da lista.")
          print("")
          input("Pressione <ENTER> para continuar...")
        else:
          uIndex = uIndex + 1
      if sInput == "2":
        if uIndex == 0:
          print("Atencao: Ja esta no inicio da lista.")
          print("")
          input("Pressione <ENTER> para continuar...")
        else:
          uIndex = uIndex - 1
      if sInput == "3":
        uPhase = 30
      if sInput == "4":
        uPhase = 40
      if sInput == "5":
        uPhase = 50
      if sInput == "6":
        uPhase = 60
    if uPhase == 30:
      print("")
      sInput = " ".join(input("Titulo: ").strip().split())
      if sInput.lower() == "c":
        uPhase = 0
      elif not sInput:
        print("Atencao: Indique um titulo.")
      else:
        sNewTitle = sInput
        uPhase = 31
    if uPhase == 31:
       print("")
       sInput = input("Modificar em todas as datas (s/n)? ").strip().lower()
       if sInput == "c":
         uPhase = 0
       elif sInput == "s":
         bAllTime = True
         uPhase = 32
       elif sInput == "n":
         bAllTime = False
         uPhase = 32
       else:
         continue
    if uPhase == 32:
      tsRes = ("update_title", (ltKeys[uIndex], sDatetimeStart, sDatetimeEnd, bAllTime, sNewTitle, lsGroupIDs), uIndex)
      bCycle = False
    if uPhase == 40:
      print("")
      print("Indique a categoria:")
      print("- 1: ERPI;")
      print("- 2: Instituicao de ensino;")
      print("- 3: Lar infantil;")
      print("- 4: Unidade de saude;")
      print("- 5: Outra.")
      print("")
      sInput = input("Categoria de grupo: ").strip().lower()
      if sInput == "c":
        uPhase = 0
      elif sInput == "":
        continue
      else:
        dClassTrans = {
          "1": "nursing_home",
          "2": "school",
          "3": "children_home",
          "4": "healthcare_unit",
          "5": "other"
          }
        if sInput not in dClassTrans:
          print("Atencao: Indique uma categoria valida.")
        else:
          sNewClass = dClassTrans[sInput]
          uPhase = 41
    if uPhase == 41:
       print("")
       sInput = input("Modificar em todas as datas (s/n)? ").strip().lower()
       if sInput == "c":
         uPhase = 0
       elif sInput == "s":
         bAllTime = True
         uPhase = 42
       elif sInput == "n":
         bAllTime = False
         uPhase = 42
       else:
         continue
    if uPhase == 42:
      tsRes = ("update_class", (ltKeys[uIndex], sDatetimeStart, sDatetimeEnd, bAllTime, sNewClass, lsGroupIDs), uIndex)
      bCycle = False
    if uPhase == 50:
       print("")
       sInput = input("Eliminar em todas as datas (s/n)? ").strip().lower()
       if sInput == "c":
         uPhase = 0
       elif sInput == "s":
         bAllTime = True
         uPhase = 51
       elif sInput == "n":
         bAllTime = False
         uPhase = 51
       else:
         continue
    if uPhase == 51:
      tsRes = ("delete", (ltKeys[uIndex], sDatetimeStart, sDatetimeEnd, bAllTime, lsGroupIDs), uIndex)
      bCycle = False
    if uPhase == 60:
      uPages = math.ceil(len(ltKeys) / uGroupListPageLen)
      uCurrPage = 0
      uPhase = 61
    if uPhase == 61:
      os.system("cls")
      print("#INSTITUTION# | #DEPARTMENT#")
      print("Ferramenta para transmissao de resultados para COVID-19")
      print("")
      print("A listar grupos na página {} de {} ({} a {})".format(str(uCurrPage + 1), str(uPages), datetime.datetime.strptime(sDatetimeStart, sStandardDatetimeFormat).strftime(sDateFormat),
datetime.datetime.strptime(sDatetimeEnd, sStandardDatetimeFormat).strftime(sDateFormat)))
      for uGroupIndex, tKey in enumerate(ltKeys):
        uGroupPage = math.floor(uGroupIndex / uGroupListPageLen)
        if uGroupPage != uCurrPage:
          continue
        sCounts = ""
        for sGroupPatientClass, lsGroupIDs in dGroups[tKey][1].items():
          sCounts = "{}{} {}".format("{}; ".format(sCounts) if sCounts else "", str(len(lsGroupIDs)), GetGroupPatientClassPlural(sGroupPatientClass).lower() if len(lsGroupIDs) != 1 else GetGroupPatientClass(sGroupPatientClass).lower())
        sGroupTitle = tKey[0]
        sGroupClass = GetGroupClass2(tKey[1])
        # print("\n# Grupo {} - {} ({}):\n  {}".format(str(uGroupIndex + 1), sGroupClass, sCounts, sGroupTitle))
        print("\n#{}: {} ({})".format(str(uGroupIndex + 1), sGroupTitle, sGroupClass))
      sInput = input("\nOpcao (numero, S/P, A/V ou C): ").strip().lower()
      if IsInt(sInput):
        uGroupSelected = int(sInput)
        uPhase = 62
      elif sInput in ["s", "p"]:
        uCurrPage = uCurrPage + 1
        if uCurrPage >= uPages:
          uCurrPage = uPages - 1
        uPhase = 61
      elif sInput in ["a", "v"]:
        if uCurrPage > 0:
          uCurrPage = uCurrPage - 1
        uPhase = 61
      elif sInput == "c":
        uPhase = 0
      else:
        continue
    if uPhase == 62:
      if uGroupSelected > 0 and uGroupSelected < len(ltKeys):
        uIndex = uGroupSelected - 1
        uPhase = 0
      else:
        input("\nAtencao: Selecione uma opcao valida.\n\nPressione <ENTER> para continuar...")
        uPhase = 60
  return tsRes

def SearchTests():
  global\
    dTests,\
    sStandardDateFormat,\
    sDateFormat,\
    sStandardDatetimeFormat,\
    sDatetimeFormat,\
    oSessionDatetime,\
    uSessionTimeout
  bProcess = True
  while bProcess:
    if (datetime.datetime.now() - oSessionDatetime).seconds > uSessionTimeout:
      return "session_timeout"
    oSessionDatetime = datetime.datetime.now() 
    os.system("cls")
    tSearchMode = InputSelectSearchMode()
    if not tSearchMode:
      bProcess = False
    elif tSearchMode[0] == "session_timeout":
      return "session_timeout"
    elif tSearchMode[0] == "date":
      UpdateTests(tSearchMode[1][0], tSearchMode[1][1], None, None, None, None, None, True)
      lsSamples = list(dTests.keys())
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "name":
      UpdateTests(None, None, tSearchMode[1], None, None, None, None, True)
      lsSamples = list(dTests.keys())
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "sample_id":
      UpdateTests(None, None, None, None, None, tSearchMode[1], None, True)
      lsSamples = list(dTests.keys())
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "record":
      UpdateTests(None, None, None, None, tSearchMode[1], None, None, True)
      lsSamples = list(dTests.keys())
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "state_id":
      UpdateTests(None, None, None, tSearchMode[1], None, None, None, True)
      lsSamples = list(dTests.keys())
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "access":
      UpdateTests(None, None, None, None, None, None, tSearchMode[1], True)
      lsSamples = list(dTests.keys())
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        dAccess = DBGetAccessFromPassword(tSearchMode[1])
        if dAccess:
          print("Registo de senha de acesso:\n- NT: {}\n- NC: {}\n- Codigo: {}".format(dAccess['token'], dAccess['sample_id'], dAccess['password']))
          print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "email_access":
      UpdateTests(None, None, None, None, None, None, tSearchMode[1], True)
      lsSamples = list(dTests.keys())
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        dAccess = DBGetAccessFromPassword(tSearchMode[1])
        if dAccess:
          print("Registo de senha de acesso:\n- NT: {}\n- NC: {}\n- Codigo: {}".format(dAccess['token'], dAccess['sample_id'], dAccess['password']))
          print("")
        input("Pressione <ENTER> para continuar...")
      else:
        bEmailSend = True
        bEmailSent = False
        while bEmailSend:
          os.system("cls")
          bSend = PrintTestEmailAccess(lsSamples[0], tSearchMode[2], tSearchMode[3])
          if bSend:
            try:
              SendEmail(lsSamples[0], tSearchMode[3], [tSearchMode[2]])
              bEmailSent = True
              UpdateTests(None, None, None, None, None, lsSamples[0], None, False)
              bEmailSend = False
            except:
              print("Erro: Nao foi possivel enviar o email. Verifique o registo.")
              print("")
              input("Pressione <ENTER> para continuar...")
          else:
            bEmailSend = False
        if bEmailSent:
          sRes = CycleTests(lsSamples)
          if sRes == "exit":
            bProcess = False
    elif tSearchMode[0] == "access_unknown":
      dRes = DBInsertAccessControlUnknown()
      CreateAccessInfo(dRes['token'], dRes['password'])
    elif tSearchMode[0] == "access_delete":
      DBDeleteAccess(tSearchMode[1])
      print("")
      print("Senha de acesso eliminada.")
      print("")
      input("Pressione <ENTER> para continuar...")
    elif tSearchMode[0] == "unsent":
      UpdateTests(tSearchMode[1][0], tSearchMode[1][1], None, None, None, None, None, True)
      lsSamples = list()
      for sSample, dTest in dTests.items():
        for dTransmission in dTest['transmissions']:
          if dTransmission['status'] == "pending" or dTransmission['status'] == "error":
            if sSample not in lsSamples:
              lsSamples.append(sSample)
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "unsent_internal":
      UpdateTests(tSearchMode[1][0], tSearchMode[1][1], None, None, None, None, None, True)
      lsSamples = list()
      for sSample, dTest in dTests.items():
        for dTransmission in dTest['transmissions']:
          if dTransmission['class'] == "internal" and (dTransmission['status'] == "pending" or dTransmission['status'] == "error"):
            if sSample not in lsSamples:
              lsSamples.append(sSample)
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "no_transmission":
      UpdateTests(tSearchMode[1][0], tSearchMode[1][1], None, None, None, None, None, True)
      lsSamples = list()
      for sSample, dTest in dTests.items():
        bOwnTransmission = False
        for dTransmission in dTest['transmissions']:
          if dTransmission['recipient_class'] in ['patient', 'caregiver', 'family']:
            bOwnTransmission = True
        if not bOwnTransmission and 'password' not in dTest:
          lsSamples.append(sSample)
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "no_transmission_2":
      UpdateTests(tSearchMode[1][0], tSearchMode[1][1], None, None, None, None, None, True)
      lsSamples = list()
      for sSample, dTest in dTests.items():
        bOwnTransmission = False
        for dTransmission in dTest['transmissions']:
          if dTransmission['recipient_class'] in ['patient', 'caregiver', 'family']:
            bOwnTransmission = True
        if not bOwnTransmission and dTest['result'] in ["negative", "error"]:
          lsSamples.append(sSample)
      if len(lsSamples) == 0:
        print("")
        print("Informacao: Nao foram encontrados testes com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        sRes = CycleTests(lsSamples)
        if sRes == "exit":
          bProcess = False
    elif tSearchMode[0] == "groups":
      sDatetimeStart = tSearchMode[1][0]
      sDatetimeEnd = tSearchMode[1][1]
      dGroups = GetGroups(sDatetimeStart, sDatetimeEnd, tSearchMode[1][2])
      if len(dGroups) == 0:
        print("")
        print("Informacao: Nao foram encontrados grupos com as definicoes selecionadas.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        bProcess2 = True
        uIndex = 0
        while bProcess2:
          bUpdateGroups = False
          tsCycleRes = CycleGroups(dGroups, sDatetimeStart, sDatetimeEnd, tSearchMode[1][2], uIndex)
          if not tsCycleRes:
            bProcess2 = False
          elif tsCycleRes[0] == "update_title":
            UpdateGroups(tsCycleRes[1][0], tsCycleRes[1][5], tsCycleRes[1][4], tsCycleRes[1][0][1], tsCycleRes[1][3])
            bUpdateGroups = True
          elif tsCycleRes[0] == "update_class":
            UpdateGroups(tsCycleRes[1][0], tsCycleRes[1][5], tsCycleRes[1][0][0], tsCycleRes[1][4], tsCycleRes[1][3])
            bUpdateGroups = True
          elif tsCycleRes[0] == "delete":
            DeleteGroups(tsCycleRes[1][0], tsCycleRes[1][4], tsCycleRes[1][3])
            bUpdateGroups = True
          if bUpdateGroups:
            uIndex = tsCycleRes[2]
            dGroups = GetGroups(sDatetimeStart, sDatetimeEnd, tSearchMode[1][2])
            if len(dGroups) == 0:
              os.system("cls")
              print("#INSTITUTION# | #DEPARTMENT#")
              print("Ferramenta para transmissao de resultados para COVID-19")
              print("")
              print("Gerir grupos")
              print("")
              print("Informacao: Nao foram encontrados mais grupos com as definicoes selecionadas.")
              print("")
              input("Pressione <ENTER> para continuar...")
              bProcess2 = False
    elif tSearchMode[0] == "email_2":
      try:
        SendEmail_2(tSearchMode[2], tSearchMode[1], 'unknown', [tSearchMode[3]])
      except:
        print("Erro: Nao foi possivel enviar o email. Verifique o registo.")
        print("")
        input("Pressione <ENTER> para continuar...")
      else:
        print("Email com o anexo {}.pdf enviado para {}.".format(tSearchMode[2], tSearchMode[3]))
        print("")
        input("Pressione <ENTER> para continuar...")
  return "session_end"

def DownloadResults(sStartDatetime, sEndDatetime, sFolder = None):
  global dTests, sReportFolder
  sActualFolder = sReportFolder
  if sFolder and sFolder != "":
    sActualFolder = sFolder
  UpdateTests(sStartDatetime, sEndDatetime, None, None, None, None, None, True)
  uCount = len(dTests)
  uIndex = 1
  for sSample, dTest in dTests.items():
    print("")
    print("A obter boletim {} ({} de {})...".format(sSample, str(uIndex), str(uCount)))
    if not os.path.isfile("{}\\{}.pdf".format(sActualFolder, sSample)):
      LISGetReport(sSample, sActualFolder)
      print("- Boletim obtido.")
    else:
      print("- Boletim ja presente.")
    uIndex = uIndex + 1

def DBCheckAccessControl(sSampleID, sToken):
  try:
    sQuery = """
      SELECT
        id AS ID,
        password AS Password
      FROM access_control
      WHERE 
        UPPER(sample_id) = {} AND
        UPPER(token) = {}
      """.format(
        GetDBString(sSampleID.strip().upper()),
        GetDBString(sToken.strip().upper())
        )
    dAccessControls = RunMySql(sQuery, True)
    bFound = False
    if len(dAccessControls) > 0:
      bFound = True
    return bFound
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro no acesso a tabela 'access_control' da base de dados.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    raise dError

def LoadOrders():
  global\
    sSourceFolder,\
    sOrderFile,\
    sOrderEncoding,\
    sOrderRE,\
    sOrderDateRE,\
    sOrderDateTimeRE,\
    lsOrderTests,\
    dOrders
  dOrders = dict()
  oFile = open("{}\\{}".format(sSourceFolder, sOrderFile), "r", encoding = sOrderEncoding)
  sFile = oFile.read()
  oMem = io.StringIO(newline=None)
  oMem.write(sFile)
  oMem.seek(0)
  bParse = True
  while bParse:
    sLine = oMem.readline()
    if not sLine:
      bParse = False
      break
    oMatch = re.match(sOrderRE, sLine)
    if oMatch:
      sSample1 = oMatch[4]
      sSample2 = oMatch[16]
      sName = oMatch[5]
      sOpen = oMatch[2]
      if sOpen:
        oMatch2 = re.match(sOrderDateTimeRE, sOpen)
        sOpen = "{}/{}/{} {}:{}".format(oMatch2[3], oMatch2[2], oMatch2[1], oMatch2[4], oMatch2[5])
      sBirthday = oMatch[6]
      if sBirthday:
        oMatch2 = re.match(sOrderDateRE, sBirthday)
        sBirthday = "{}/{}/{}".format(oMatch2[3], oMatch2[2], oMatch2[1])
      sTests = oMatch[17].lower()
      lsTests = sTests.split(",")
      bFound = False
      for sTest in lsOrderTests:
        if sTest in lsTests:
          bFound = True
          break
      if bFound:
        dOrders[sSample2] = dict()
        dOrders[sSample2]['name'] = sName
        dOrders[sSample2]['birthday'] = sBirthday
        dOrders[sSample2]['activation_datetime'] = sOpen
        dOrders[sSample2]['tests'] = lsTests
        dOrders[sSample2]['accession'] = sSample1
  return

Log("#INSTITUTION# | #DEPARTMENT#")
Log("Ferramenta para transmissao de resultados para COVID-19")
Log("-------------------------------------------------------")
Log("")
Log("Data e hora: " + datetime.datetime.now().strftime(sTimestampFormat))
Log("")
print("")

try:
  LoadConfigXML()
  MySqlCursorStart()
  #DBResetAccessControl()
except Exception as dError:
  Log("")
  Log("Erro: Ocorreu um erro na inicializacao.")
  Log("Mensagem de erro:\n" + str(dError))
  Log("")
  print("Erro: Ocorreu um erro na inicializacao. Verifique o registo.")
  print("")
  Exit()

bSession = True
while bSession:
  try:
    if not Login():
      Exit()
  except Exception as dError:
    Log("")
    Log("Erro: Ocorreu um erro na inicializacao.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    print("Erro: Ocorreu um erro na inicializacao. Verifique o registo.")
    print("")
    Exit()
  try:
    random.seed(None)
    sSessionRes = SearchTests()
    if sSessionRes == "session_timeout":
      print("")
      print("Atenção: A sua sessão terminou por tempo de inatividade.")
      print("")
      input("Pressione <ENTER> para continuar...")
    else:
      bSession = False
  except Exception as dError:
    Log("")
    Log("Erro: A aplicacao foi terminada devido a um erro.")
    Log("Mensagem de erro:\n" + str(dError))
    Log("")
    print("Erro: A aplicacao foi terminada devido a um erro. Verifique o registo.")
    print("")
    bSession = False
    MySqlCursorClose()
    Exit()

MySqlCursorClose()
Exit()
