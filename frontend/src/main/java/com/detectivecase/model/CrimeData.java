package com.detectivecase.model;

import lombok.Data;

@Data
public class CrimeData {
  private String complaintId;
  private String offenseDescription;
  private String borough;
  private String date;
  private Double latitude;
  private Double longitude;
  private String premisesType;
}