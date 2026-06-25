func GetReportPhysicalCapitalHandler(c *gin.Context) {

 var req models.ReqDashboard
 if err := c.ShouldBindJSON(&req); err != nil {
  c.JSON(http.StatusBadRequest, models.ErrorResponse{
   Error: err.Error()})
  return
 }

 var resp models.RespFiveDimensionsReport

 if req.ProvCode > 0 {

  connMssql := db.GetConnectionMSSQLDB(req.ProvShort)
  if connMssql == nil {
   c.JSON(http.StatusInternalServerError, models.ErrorResponse{
    Error: "Cannot connect to database MSSQ"})
   return
  }
  defer connMssql.Close()

  resp.Title = models.Dimensions{Label: "ทุนกายภาพ",
   Normal:    0,
   Fair:      0,
   Difficult: 0,
   VeryHard:  0,
  }

  table := "survey_b"
  channel := "ch2"

  //###################
  var fiveDimensions1 models.RespFiveDimensions
  fiveDimensions1.Title = models.Dimensions{Label: "",
   Normal:    0,
   Fair:      0,
   Difficult: 0,
   VeryHard:  0,
  }
  optionsListB2 := []models.MasterXOption{
   {
    SubTitle: "การมีบ้านเป็นของตนเอง (B2)",
    XOption: []models.XOption{
     {FieldValue: 0, XLabel: "0 - ไม่มีบ้านพักอาศัยเป็นหลักแหล่ง", FieldName: "b2"},
     {FieldValue: 1, XLabel: "1 - อาศัยอยู่กับผู้อื่น", FieldName: "b2"},
     {FieldValue: 2, XLabel: "2 - เช่าบ้านอยู่", FieldName: "b2"},
     {FieldValue: 3, XLabel: "3 - ปลูกบ้านในที่ดินผู้อื่น", FieldName: "b2"},
     {FieldValue: 4, XLabel: "4 - มีบ้านและที่ดินเป็นของตนเอง", FieldName: "b2"},
     {FieldValue: -1, XLabel: "ไม่ระบุ", FieldName: "b2"},
    },
   },
  }

  for _, optionsList := range optionsListB2 {

   data, err := repository.GetReportFiveDimensionsSingle(connMssql, req.ProvCode, *req.DistrictCode, *req.SubDistrictCode, *req.Year, optionsList.SubTitle, table, channel, optionsList.XOption)
   if err != nil {
    c.JSON(http.StatusInternalServerError, models.ErrorResponse{
     Error: err.Error()})
    return
   }
   fiveDimensions1.DataList = append(fiveDimensions1.DataList, data)
  }

  resp.Data = append(resp.Data, fiveDimensions1)

  //####################

  var fiveDimensions2 models.RespFiveDimensions
  fiveDimensions2.Title = models.Dimensions{Label: "",
   Normal:    0,
   Fair:      0,
   Difficult: 0,
   VeryHard:  0,
  }
  optionsListB3 := []models.MasterXOption{
   {
    SubTitle: "สภาพของบ้านที่อยู่อาศัยในปัจจุบัน (B3)",
    XOption: []models.XOption{
     {FieldValue: 1, XLabel: "1 - สภาพทรุดโทรม", FieldName: "b3"},
     {FieldValue: 2, XLabel: "2 - มีสภาพแข็งแรงปานกลาง", FieldName: "b3"},
     {FieldValue: 3, XLabel: "3 - มีสภาพมั่นคงแข็งแรง", FieldName: "b3"},
     {FieldValue: -1, XLabel: "ไม่ระบุ", FieldName: "b3"},
    },
   },
  }

  for _, optionsList := range optionsListB3 {

   data, err := repository.GetReportFiveDimensionsSingle(connMssql, req.ProvCode, *req.DistrictCode, *req.SubDistrictCode, *req.Year, optionsList.SubTitle, table, channel, optionsList.XOption)
   if err != nil {
    c.JSON(http.StatusInternalServerError, models.ErrorResponse{
     Error: err.Error()})
    return
   }
   fiveDimensions2.DataList = append(fiveDimensions2.DataList, data)
  }

  resp.Data = append(resp.Data, fiveDimensions2)

  //####################

  var fiveDimensions3 models.RespFiveDimensions
  fiveDimensions3.Title = models.Dimensions{Label: "",
   Normal:    0,
   Fair:      0,
   Difficult: 0,
   VeryHard:  0,
  }
  optionsListB8 := []models.MasterXOption{
   {
    SubTitle: "สถานภาพการทำงาน (B8)",
    XOption: []models.XOption{
     {FieldValue: 0, XLabel: "0 - ไม่มีที่ทำกินทางเกษตร/ไม่ได้ทำการเกษตร", FieldName: "b8_0"},
     {FieldValue: 1, XLabel: "1 - มีพื้นที่ทำกินเป็นของตนเอง โดยมีเอกสารสิทธิ์แสดงความเป็นเจ้าของ", FieldName: "b8_1"},
     {FieldValue: 2, XLabel: "2 - มีพื้นที่ทำกินที่มีเอกสารแสดงสิทธิ์การครอบครอง(ส.ค.1, สปก)", FieldName: "b8_2"},
     {FieldValue: 3, XLabel: "3 - มีพื้นที่ทำกินโดยไม่มีเอกสารแสดงความเป็นเจ้าของ (ภบท.5/6/11) เช่น เขตป่าสงวนฯ/อุทยานฯหรืออื่น ๆ", FieldName: "b8_3"},
     {FieldValue: 4, XLabel: "4 - ไม่มีพื้นที่ทำกินเป็นของตนเองแต่อาศัยพื้นที่ของบุคคลอื่นทำ โดยไม่มีค่าเช่า", FieldName: "b8_4"},
     {FieldValue: 5, XLabel: "5 - เช่าทำกิน", FieldName: "b8_5"},
     {FieldValue: 6, XLabel: "6 - -", FieldName: "b8_6"},
     {FieldValue: -1, XLabel: "ไม่ระบุ", FieldName: "b8"},
    },
   },
  }

  for _, optionsList := range optionsListB8 {

   data, err := repository.GetReportFiveDimensionsMore(connMssql, req.ProvCode, *req.DistrictCode, *req.SubDistrictCode, *req.Year, optionsList.SubTitle, table, channel, optionsList.XOption)
   if err != nil {
    c.JSON(http.StatusInternalServerError, models.ErrorResponse{
     Error: err.Error()})
    return
   }
   fiveDimensions3.DataList = append(fiveDimensions3.DataList, data)
  }

  resp.Data = append(resp.Data, fiveDimensions3)

  //####################

 }

 c.JSON(http.StatusOK, resp)
}