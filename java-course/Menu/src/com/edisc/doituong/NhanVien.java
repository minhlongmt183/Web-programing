package com.edisc.doituong;

public class NhanVien {
	private String ten;
	private String diaChi;
	private int tuoi;
	
	// constructor 
	public NhanVien() {
	}
	
	public NhanVien(String ten, String diaChi, int tuoi) {
		this.ten = ten;
		this.diaChi = diaChi;
		this.tuoi = tuoi;
	}
	
	
	public String getTen() {
		return ten;
	}
	public void setTen(String ten) {
		this.ten = ten;
	}
	public String getDiaChi() {
		return diaChi;
	}
	public void setDiaChi(String diaChi) {
		this.diaChi = diaChi;
	}
	public int getTuoi() {
		return tuoi;
	}
	public void setTuoi(int tuoi) {
		this.tuoi = tuoi;
	}
	
	

}
