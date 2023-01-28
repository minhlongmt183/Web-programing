package com.edisc.domain;

public class Person {
	String ten; // default
	private int tuoi; // khai bao private
	public String diaChi;
	protected String danToc;
	
	
	
	public String getTen() {
		return ten;
	}

	public void setTen(String ten) {
		this.ten = ten;
	}

	public int getTuoi() {
		return tuoi;
	}

	public void setTuoi(int tuoi) {
		this.tuoi = tuoi;
	}

	public String getDiaChi() {
		return diaChi;
	}

	public void setDiaChi(String diaChi) {
		this.diaChi = diaChi;
	}

	public String getDanToc() {
		return danToc;
	}

	public void setDanToc(String danToc) {
		this.danToc = danToc;
	}

	public void anUong() {}
	
	private void noiChuyen() {}
	
	protected void diLai() {}
	
	void troChuyen() {}

}
